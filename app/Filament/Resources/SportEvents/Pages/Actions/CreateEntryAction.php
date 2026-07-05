<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\EntryFormFields;
use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use App\Services\OrisApiService;
use App\Services\SportEvents\Entries\EntryCreator;
use App\Services\SportEvents\Entries\EntryResult;
use App\Services\SportEvents\Entries\RelaySlotManager;
use App\Shared\Helpers\EmptyType;
use Filament\Actions\Action as ActionAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CreateEntryAction
{
    public function __construct(
        private readonly SportEvent $sportEvent,
    ) {
    }

    public function register(bool $registerAll = false): ActionAction
    {
        $sportEvent = $this->sportEvent;

        return ActionAction::make($registerAll ? 'createEventEntryFull' : 'createEventEntry')
            ->action(function (array $data) use ($sportEvent): void {
                $result = EntryCreator::make()->create($sportEvent, $data);
                $this->sendNotification($result, $sportEvent);
            })
            ->disabled(function () use ($registerAll, $sportEvent): bool {
                if ($registerAll) {
                    return $sportEvent->cancelled === true;
                }

                return EmptyType::arrayEmpty((new UserRaceProfiles())->getUserRaceProfiles($sportEvent)->toArray())
                    || $sportEvent->cancelled
                    || ! Auth::user()?->canCreateEntry();
            })
            ->color($registerAll ? 'gray' : 'primary')
            ->label($registerAll ? 'Přihlásit kohokoliv' : 'Přihlásit na závod')
            ->icon($registerAll ? 'heroicon-o-users' : 'heroicon-o-plus-circle')
            ->modalHeading('Přihlášení na závod')
            ->modalDescription('Vyber závodní profil, vyhledej vhodné kategorie a přihlas se.')
            ->modalSubmitActionLabel('Přihlásit')
            ->schema($this->formSchema($registerAll));
    }

    /** @return list<mixed> */
    private function formSchema(bool $registerAll): array
    {
        $sportEvent = $this->sportEvent;

        return [
            Select::make('raceProfileId')
                ->label('Vyberte závodní profil')
                ->options(
                    $registerAll
                        ? (new UserRaceProfiles())->getUserRaceProfiles($sportEvent, true)
                        : (new UserRaceProfiles())->getUserRaceProfiles($sportEvent)
                )
                ->allowHtml()
                ->default(function () use ($sportEvent): ?int {
                    $userProfileRecords = (new UserRaceProfiles())->getUserRaceProfiles($sportEvent);
                    if ($sportEvent->oris_id === null && count($userProfileRecords) === 1) {
                        return (int) array_key_first($userProfileRecords->toArray());
                    }

                    return null;
                })
                ->required()
                ->live()
                ->searchable()
                ->afterStateUpdated(function (string $state, Set $set) use ($sportEvent): void {
                    if (! ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries)) {
                        $userProfile = UserRaceProfile::find((int) $state);
                        $set('si', $userProfile?->si);

                        return;
                    }

                    try {
                        $userProfile = UserRaceProfile::where('oris_id', '=', (int) $state)->first();

                        $params = [
                            'format' => 'json',
                            'method' => 'getValidClasses',
                            'clubuser' => $userProfile->club_user_id ?? '',
                            'comp' => $sportEvent->oris_id,
                        ];

                        $orisResponse = Http::get(OrisApiService::ORIS_API_URL, $params)
                            ->throw()
                            ->json('Data');

                        if ($orisResponse === null) {
                            Notification::make()
                                ->title('Na závod není možné se uvedeným závodním profilem přihlásit')
                                ->body('Překontrolujte zdali mát v závodním profilu vyplněno ORISID, dále zkontrolujte platnou registraci na daný rok,
                                    Na některé závody není možné jako neregistrovaný se přihlásit.')
                                ->danger()
                                ->seconds(10)
                                ->send();
                        }

                        $selectData = [];
                        if (is_array($orisResponse) && count($orisResponse) > 0) {
                            foreach ($orisResponse as $class) {
                                $selectData[$class['ID']] = $class['ClassDesc'];
                            }
                        }
                    } catch (RequestException $e) {
                        Notification::make()
                            ->title('Nepodařilo se načíst data.')
                            ->danger()
                            ->duration(8);

                        return;
                    }

                    Notification::make()
                        ->title('ORIS v pořádku vrátil požadovaná data.')
                        ->success();

                    $set('specific_response_class_id', $selectData);
                    $set('si', $userProfile?->si);
                }),

            Select::make('classId')
                ->label('Vyber kategorii')
                ->options(function (callable $get) use ($sportEvent): array {
                    if ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
                        return $get('specific_response_class_id') ?? [];
                    }

                    return EntryFormFields::localClassOptions($sportEvent);
                })
                ->searchable()
                ->allowHtml()
                ->required(fn (): bool => ! $sportEvent->isRelayDiscipline())
                ->visible(fn (): bool => ! $sportEvent->isRelayDiscipline())
                ->loadingMessage('Nahrávám kategorie...'),

            Select::make('relayTeamMemberId')
                ->label('Volné místo v týmu')
                ->options(fn () => (new RelaySlotManager())->availableSlots($sportEvent))
                ->allowHtml()
                ->searchable()
                ->required(fn (): bool => $sportEvent->isRelayDiscipline())
                ->visible(fn (): bool => $sportEvent->isRelayDiscipline()),

            EntryFormFields::siFields(),

            EntryFormFields::stagesField($sportEvent),

            EntryFormFields::additionalInformationSection(),
        ];
    }

    private function sendNotification(EntryResult $result, SportEvent $sportEvent): void
    {
        if ($sportEvent->isRelayDiscipline()) {
            if ($result->success) {
                Notification::make()
                    ->title('Přihláška byla úspěšně vytvořena')
                    ->body('Přihláška byla provedena do interní relay sestavy.')
                    ->success()->seconds(8)->send();
            } else {
                Notification::make()
                    ->title('Přihlášku se nepodařilo vytvořit')
                    ->body('Vybraný tým je pravděpodobně již obsazen nebo neexistuje.')
                    ->warning()->seconds(8)->send();
            }

            return;
        }

        $profileName = $result->userRaceProfile?->user_race_full_name;
        $className = $result->sportClass?->name;
        $title = 'Přihláška  '.$profileName.' do kategorie: '.$className;

        if ($result->orisStatusError !== null) {
            Notification::make()
                ->title($title)
                ->body('Nebyla provedena. ORIS vrátil zprávu: '.$result->orisStatusError)
                ->warning()->seconds(8)->send();

            return;
        }

        if ($result->success && $result->orisEventId !== null) {
            Notification::make()
                ->title($title)
                ->body('Přihlášku si zkontroluj na stránkách závodu přímo v ORISu.')
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

        if ($result->success) {
            Notification::make()
                ->title($title)
                ->body('Přihláška byla provedena pouze v interním systému')
                ->success()->seconds(8)->send();
        }
    }
}
