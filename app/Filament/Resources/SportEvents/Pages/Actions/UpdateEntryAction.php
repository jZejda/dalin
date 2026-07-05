<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\EntryFormFields;
use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
use App\Filament\Resources\SportEvents\Service\SportEventService;
use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\OrisApiService;
use App\Services\SportEvents\Entries\EntryUpdater;
use App\Services\SportEvents\Entries\UpdateResult;
use App\Shared\Helpers\AppHelper;
use Filament\Actions\Action as ActionAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UpdateEntryAction
{
    public function make(): ActionAction
    {
        return ActionAction::make('update_user_entry')
            ->hidden(fn (UserEntry $record): bool => $this->shouldHide($record))
            ->action(function (UserEntry $record, array $data): void {
                $profile = $record->userRaceProfile;
                $profileName = $profile instanceof UserRaceProfile ? $profile->user_race_full_name : '';
                $result = EntryUpdater::make()->update($record, $data);
                $this->sendNotification($result, $profileName);
            })
            ->color('gray')
            ->label('Upravit')
            ->icon('heroicon-o-pencil-square')
            ->disabled(fn (UserEntry $record): bool => $record->sportEvent instanceof SportEvent
                && AppHelper::allowModifyUserEntry($record->sportEvent)
                && ! AppHelper::allowModifyUserEntryAfterDeadline($record->sportEvent))
            ->modalHeading(function (UserEntry $record): string {
                $profile = $record->userRaceProfile;
                $name = $profile instanceof UserRaceProfile ? $profile->user_race_full_name : '';

                return $name.' - úprava přihlášky';
            })
            ->modalDescription('Uprav údaje přihlášky a potvrď uložením.')
            ->modalSubmitActionLabel('Uložit změny')
            ->fillForm(fn (UserEntry $record): array => $this->fillFormData($record))
            ->schema(fn (UserEntry $record): array => $this->formSchema($record));
    }

    public function shouldHide(UserEntry $userEntry): bool
    {
        if ($userEntry->sportEvent?->isRelayDiscipline() === true) {
            return true;
        }

        if ($userEntry->entry_status === EntryStatus::Cancel) {
            return true;
        }

        if (Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::EventOrganizer])) {
            return false;
        }

        if ($userEntry->userRaceProfile !== null && UserRaceProfiles::allowAnotherUserCareTheirUserRaceProfile($userEntry->userRaceProfile)) {
            return false;
        }

        if ($userEntry->userRaceProfile?->user?->id !== Auth::user()?->id) {
            return true;
        }

        return false;
    }

    /** @return array<string, mixed> */
    private function fillFormData(UserEntry $record): array
    {
        $sportEvent = $record->sportEvent;

        $data = [
            'classId' => $this->currentClassId($record),
            'si' => $record->si,
            'rent_si' => $record->rent_si ? 1 : 0,
            'note' => $record->note,
            'club_note' => $record->club_note,
            'requested_start' => $record->requested_start,
        ];

        if ($sportEvent instanceof SportEvent && ! ($sportEvent->stages === null || $sportEvent->stages === 0)) {
            $data['entry_stages'] = $record->entry_stages
                ?? (new SportEventService())->getMultiEventDefaultOptions($sportEvent);
        }

        return $data;
    }

    /** @return list<mixed> */
    private function formSchema(UserEntry $record): array
    {
        $sportEvent = $record->sportEvent;
        if (! $sportEvent instanceof SportEvent) {
            return [];
        }

        return [
            Select::make('classId')
                ->label('Vyber kategorii')
                ->options(fn (): array => $this->classOptions($record, $sportEvent))
                ->searchable()
                ->allowHtml()
                ->required()
                ->loadingMessage('Nahrávám kategorie...'),

            EntryFormFields::siFields(),

            EntryFormFields::stagesField($sportEvent),

            EntryFormFields::additionalInformationSection(),
        ];
    }

    /**
     * ID of the entry's current class as used by the form select:
     * ORIS class ID for ORIS events, local SportClass ID otherwise.
     */
    private function currentClassId(UserEntry $record): ?int
    {
        $sportEvent = $record->sportEvent;
        if (! $sportEvent instanceof SportEvent) {
            return null;
        }

        $sportClass = SportClass::query()
            ->where('sport_event_id', '=', $sportEvent->id)
            ->where('class_definition_id', '=', $record->class_definition_id)
            ->first();

        if ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
            return $sportClass?->oris_id;
        }

        return $sportClass?->id;
    }

    /** @return array<int|string, string> */
    private function classOptions(UserEntry $record, SportEvent $sportEvent): array
    {
        if ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
            return $this->orisClassOptions($record, $sportEvent);
        }

        return EntryFormFields::localClassOptions($sportEvent);
    }

    /**
     * Valid ORIS classes for the entry's race profile. Falls back to the
     * entry's current class when ORIS is unreachable or returns no data.
     *
     * @return array<int|string, string>
     */
    private function orisClassOptions(UserEntry $record, SportEvent $sportEvent): array
    {
        $fallback = [];
        $currentClassId = $this->currentClassId($record);
        if ($currentClassId !== null) {
            $fallback[$currentClassId] = (string) $record->class_name;
        }

        $clubUserId = $record->userRaceProfile?->club_user_id;
        if ($clubUserId === null) {
            return $fallback;
        }

        try {
            $orisResponse = Http::get(OrisApiService::ORIS_API_URL, [
                'format' => 'json',
                'method' => 'getValidClasses',
                'clubuser' => $clubUserId,
                'comp' => $sportEvent->oris_id,
            ])->throw()->json('Data');
        } catch (RequestException) {
            return $fallback;
        }

        if (! is_array($orisResponse) || count($orisResponse) === 0) {
            return $fallback;
        }

        $selectData = [];
        foreach ($orisResponse as $class) {
            if (is_array($class) && isset($class['ID'], $class['ClassDesc'])) {
                $selectData[(int) $class['ID']] = (string) $class['ClassDesc'];
            }
        }

        return $selectData === [] ? $fallback : $selectData;
    }

    private function sendNotification(UpdateResult $result, string $profileName): void
    {
        if (! $result->success) {
            Notification::make()
                ->title('Úprava přihlášky '.$profileName.' se nezdařila')
                ->body($result->orisStatusError !== null
                    ? 'ORIS vrátil zprávu: '.$result->orisStatusError
                    : 'Zkus akci zopakovat, případně kontaktuj správce.')
                ->warning()->seconds(10)->send();

            return;
        }

        if ($result->wasOrisEntry) {
            Notification::make()
                ->title('Přihláška '.$profileName.' byla upravena')
                ->body('ORIS potvrdil úpravu, změnu si můžeš zkontrolovat na stránce závodu.')
                ->success()
                ->actions([
                    ActionAction::make('view')
                        ->label('Přejít na stránku závodu')
                        ->button()->openUrlInNewTab()
                        ->url(OrisApiService::ORIS_URL.'/PrehledPrihlasenych?id='.$result->orisEventId),
                ])
                ->seconds(15)->send();

            return;
        }

        Notification::make()
            ->title('Přihláška '.$profileName.' byla upravena')
            ->body('Úprava proběhla pouze v našem systému.')
            ->success()->seconds(8)->send();
    }
}
