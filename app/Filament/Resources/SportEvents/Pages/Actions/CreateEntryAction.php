<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
use App\Filament\Resources\SportEvents\Service\SportEventService;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use App\Services\OrisApiService;
use App\Services\SportEvents\Entries\EntryCreator;
use App\Services\SportEvents\Entries\EntryResult;
use App\Services\SportEvents\Entries\RelaySlotManager;
use App\Shared\Helpers\AppHelper;
use App\Shared\Helpers\EmptyType;
use Filament\Actions\Action as ActionAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

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

                    $eventClasses = SportClass::where('sport_event_id', '=', $sportEvent->id)->get();
                    $classes = [];
                    foreach ($eventClasses as $eventClass) {
                        $classDefinitionName = SportClassDefinition::where('id', '=', $eventClass->class_definition_id)->first();
                        $label = $classDefinitionName instanceof SportClassDefinition
                            ? $classDefinitionName->classDefinitionFullLabel
                            : '';
                        $classes[$eventClass->id] = '<span class="font-medium">'.e($eventClass->name)
                            .'</span> <span class="text-gray-400"> | '
                            .e($label)
                            .'</span>';
                    }

                    return $classes;
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

            Grid::make()->schema([
                TextInput::make('si')
                    ->label('Číslo SI čipu')
                    ->numeric(),
                ToggleButtons::make('rent_si')
                    ->label('Půjčit čip')
                    ->options([0 => 'Ne', 1 => 'Ano '])
                    ->default(0)
                    ->inline(),
            ])->columns(2),

            Grid::make()->schema([
                Select::make('entry_stages')
                    ->label('Zvol etapy')
                    ->options(function () use ($sportEvent): array {
                        if ($sportEvent->stages !== null && $sportEvent->sport_id >= 1) {
                            return (new SportEventService())->getMultiEventStagesOptions($sportEvent);
                        }

                        return [];
                    })
                    ->multiple()
                    ->default(fn (): array => (new SportEventService())->getMultiEventDefaultOptions($sportEvent))
                    ->minItems(fn (): int => ($sportEvent->stages === null || $sportEvent->stages === 0) ? 0 : 1)
                    ->required(fn (): bool => ! ($sportEvent->stages === null || $sportEvent->stages === 0))
                    ->visible(fn (): bool => ! ($sportEvent->stages === null || $sportEvent->stages === 0)),
            ])->columns(1),

            Section::make('Doplňkové informace')
                ->description('Další informace k přihlášce doplň po rozkliknutí.')
                ->schema([
                    Grid::make()->schema([
                        TextInput::make('note')
                            ->label('Poznámka')
                            ->hint('Poznámka pořadateli.'),
                        TextInput::make('club_note')
                            ->label('Klubová poznámka')
                            ->hint('Interní poznámka.'),
                        Grid::make()->columnSpanFull()->schema([
                            TextInput::make('requested_start')
                                ->label('Požadovaný start')
                                ->hint(fn (): HtmlString => new HtmlString(
                                    '<a href="'.AppHelper::getPageHelpUrl('jak-se-prihlasit-na-oris-zavod.html').'" target="_blank">Prosím čtěte nápovědu.</a>'
                                ))
                                ->hintColor('primary')
                                ->hintIcon('heroicon-m-question-mark-circle')
                                ->columnSpan(['sm' => 6, 'xl' => 4]),
                            Select::make('startListHint')
                                ->label('Vzor start požadavku')
                                ->options([
                                    'Jednoetapové' => [
                                        'E0_early' => 'E0 - Brzy',
                                        'E0_late' => 'E0 - Pozdě',
                                        'E0_similarly' => 'E0 - Podobně',
                                        'E0_variously' => 'E0 - Různě',
                                        'E0_note' => 'E0 - Poznámka',
                                    ],
                                    'Etapové' => [
                                        'E123_early' => 'E123 - Brzy',
                                        'E123_late' => 'E123 - Pozdě',
                                        'E123_similarly' => 'E123 - Podobně',
                                        'E123_variously' => 'E123 - Různě',
                                        'E123_note' => 'E123 - Poznámka',
                                    ],
                                ])
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set): void {
                                    $pattern = match ($state) {
                                        'E0_early' => '(E0;brzy;)',
                                        'E0_late' => '(E0;pozde;)',
                                        'E0_similarly' => '(E0;podobne;REG_CISLO)',
                                        'E0_variously' => '(E0;ruzne;REG_CISLO)',
                                        'E0_note' => '(E0;ruzne;POZNAMKA)',
                                        'E123_early' => '(E123;brzy;)',
                                        'E123_late' => '(E123;pozde;)',
                                        'E123_similarly' => '(E123;podobne;REG_CISLO)',
                                        'E123_variously' => '(E123;ruzne;REG_CISLO)',
                                        'E123_note' => '(E123;ruzne;POZNAMKA)',
                                        default => '',
                                    };

                                    if ($pattern !== '') {
                                        $set('requested_start', $pattern);
                                    }
                                })
                                ->columnSpan(['sm' => 6, 'xl' => 2]),
                        ])->columns(6),
                    ])->columns(2),
                ])
                ->collapsible()
                ->persistCollapsed()
                ->id('entry_additional_information'),
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
