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
use Livewire\Component;

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
            ->action(function (array $data, ?Component $livewire) use ($sportEvent): void {
                $result = EntryCreator::make()->create($sportEvent, $data);
                $this->sendNotification($result, $sportEvent);

                if ($result->success) {
                    $livewire?->dispatch('entry-created');
                }
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
            ->label($registerAll ? __('sport-event.actions.create_entry.label_all') : __('sport-event.actions.create_entry.label_self'))
            ->icon($registerAll ? 'heroicon-o-users' : 'heroicon-o-plus-circle')
            ->modalHeading(__('sport-event.actions.create_entry.modal_heading'))
            ->modalDescription(__('sport-event.actions.create_entry.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.create_entry.modal_submit'))
            ->schema($this->formSchema($registerAll));
    }

    /** @return list<mixed> */
    private function formSchema(bool $registerAll): array
    {
        $sportEvent = $this->sportEvent;

        return [
            Select::make('raceProfileId')
                ->label(__('sport-event.actions.create_entry.race_profile'))
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
                                ->title(__('sport-event.actions.create_entry.notification_title_ineligible'))
                                ->body(__('sport-event.actions.create_entry.notification_body_ineligible'))
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
                            ->title(__('sport-event.common.oris_fetch_error'))
                            ->danger()
                            ->duration(8);

                        return;
                    }

                    Notification::make()
                        ->title(__('sport-event.actions.create_entry.notification_title_oris_ok'))
                        ->success();

                    $set('specific_response_class_id', $selectData);
                    $set('si', $userProfile?->si);
                }),

            Select::make('classId')
                ->label(__('sport-event.actions.create_entry.class'))
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
                ->loadingMessage(__('sport-event.actions.create_entry.loading_classes')),

            Select::make('relayTeamMemberId')
                ->label(__('sport-event.actions.create_entry.relay_slot'))
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
                    ->title(__('sport-event.actions.create_entry.notification_title_relay_success'))
                    ->body(__('sport-event.actions.create_entry.notification_body_relay_success'))
                    ->success()->seconds(8)->send();
            } else {
                Notification::make()
                    ->title(__('sport-event.actions.create_entry.notification_title_relay_failed'))
                    ->body(__('sport-event.actions.create_entry.notification_body_relay_failed'))
                    ->warning()->seconds(8)->send();
            }

            return;
        }

        $profileName = $result->userRaceProfile?->user_race_full_name;
        $className = $result->sportClass?->name;
        $title = __('sport-event.actions.create_entry.notification_title', [
            'profile' => $profileName,
            'class' => $className,
        ]);

        if ($result->orisStatusError !== null) {
            Notification::make()
                ->title($title)
                ->body(__('sport-event.actions.create_entry.notification_body_oris_error', ['error' => $result->orisStatusError]))
                ->warning()->seconds(8)->send();

            return;
        }

        if ($result->success && $result->orisEventId !== null) {
            Notification::make()
                ->title($title)
                ->body(__('sport-event.actions.create_entry.notification_body_oris_success'))
                ->success()
                ->actions([
                    ActionAction::make('view')
                        ->label(__('sport-event.actions.create_entry.view_event_action'))
                        ->button()->openUrlInNewTab()
                        ->url(OrisApiService::ORIS_URL.'/PrehledPrihlasenych?id='.$result->orisEventId),
                ])
                ->seconds(15)->send();

            return;
        }

        if ($result->success) {
            Notification::make()
                ->title($title)
                ->body(__('sport-event.actions.create_entry.notification_body_local_success'))
                ->success()->seconds(8)->send();
        }
    }
}
