<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

use App\Filament\Resources\SportEvents\Pages\Actions\ExportsData;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Throwable;
use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Filament\Resources\SportEvents\Pages\Actions\EntrySendMail;
use App\Filament\Resources\SportEvents\Pages\Actions\EntryUpdateEvent;
use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
use App\Filament\Resources\SportEvents\Service\SportEventService;
use App\Http\Components\Oris\GuzzleClient;
use App\Http\Components\Oris\ManageEntry;
use App\Http\Components\Oris\Response\CreateEntry;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\RelayTeamMember;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use App\Shared\Helpers\EmptyType;
use Filament\Actions\Action as ActionAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class EntrySportEvent extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithRecord;
    use InteractsWithTable;

    public string|int|null|Model $record;

    protected static string $resource = SportEventResource::class;

    protected string $view = 'filament.resources.sport-event-resource.pages.event-entry';

    public string $back_button_url = '/admin/sport-events';

    public function booted()
    {
        //$this->beforeBooted();

        // @todo refactor check Filament::user ability.
        if (! Auth::user()?->hasRole(User::ROLE_MEMBER.'|'.User::ROLE_EVENT_MASTER.'|'.User::ROLE_SUPER_ADMIN)) {
            $this->notify('warning', __('filament-shield::filament-shield.forbidden'));

            $this->beforeShieldRedirects();

            redirect($this->getShieldRedirectPath());

            return;
        }

        if (method_exists(parent::class, 'booted')) {
            parent::booted();
        }
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected array $rules = [
        'oris_class_id' => 'required|min:3',
    ];

    public function getTitle(): string
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return 'Detail závodu - '.$sportEvent->name;
    }

    protected function getHeaderActions(): array
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        $makeExportModal = new ExportsData($sportEvent);
        $sendMailModal = new EntrySendMail($sportEvent);
        $updateEvent = new EntryUpdateEvent($sportEvent);
        $registerAnyone = Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::EventOrganizer]) ? $this->getOrisEvent(true) : null;
        $sendEmail = Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::SuperAdmin, AppRoles::EventOrganizer])
            ? $sendMailModal->sendNotification()
            : null;

        $makeExport = Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::SuperAdmin, AppRoles::EventOrganizer])
            ? $makeExportModal->makeExport()
            : null;

        $defaultActions = [
            $this->getOrisEvent(),
        ];

        if (! is_null($sportEvent->oris_id)) {
            $defaultActions[] = $updateEvent->showUpdateEventFromOris();
        }

        if (! is_null($registerAnyone)) {
            $defaultActions[] = $registerAnyone;
        }

        if (! is_null($sendEmail)) {
            $defaultActions[] = $sendEmail;
        }
        if (! is_null($makeExport)) {
            $defaultActions[] = $makeExport;
        }

        return $defaultActions;
    }

    protected function getTableQuery(): Builder
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return UserEntry::where('sport_event_id', '=', $sportEvent->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('class_name')
                ->label('Kategorie')
                ->description(function (UserEntry $record): string {
                    $sportClass = SportClass::query()
                        ->where('sport_event_id', $record->sport_event_id)
                        ->where('name', $record->class_name)
                        ->first();
                    if ($sportClass === null) {
                        return '';
                    }
                    $parts = array_filter([
                        $sportClass->distance ? $sportClass->distance . 'km' : null,
                        $sportClass->climbing ? $sportClass->climbing . 'm' : null,
                        $sportClass->controls ? $sportClass->controls . 'k' : null,
                    ]);
                    return implode(' | ', $parts);
                })
                ->searchable()
                ->sortable(),
            TextColumn::make('relayTeamMember.relayTeam.name')
                ->label('Tým')
                ->formatStateUsing(function ($state, UserEntry $record): string {
                    $slot = $record->relayTeamMember?->slot;
                    if ($state === null) {
                        return '—';
                    }

                    return $slot !== null ? $state.' (slot '.$slot.')' : $state;
                })
                ->placeholder('—'),
            TextColumn::make('userRaceProfile.UserRaceFullName')
                ->label('Registrace')
                ->html()
                ->formatStateUsing(fn ($state, UserEntry $record): HtmlString => new HtmlString(
                    (string) view('components.user-race-profile-badges', [
                        'profiles' => collect([$record->userRaceProfile])->filter(),
                        'size' => 'text-xs'
                    ])
                ))
                ->description(function (UserEntry $record): HtmlString {
                    $name = $record->userRaceProfile?->user?->name;
                    if ($name === null) {
                        return new HtmlString('');
                    }
                    $isCurrentUser = $name === Auth::user()?->name;
                    $classes = $isCurrentUser
                        ? 'inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                        : 'inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                    return new HtmlString('<span class="' . $classes . '">' . e($name) . '</span>');
                })
                ->searchable(),
            TextColumn::make('note')
                ->label('Interní poznámka')
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->note ?? ''),
            TextColumn::make('club_note')
                ->label('Klubová poznámka')
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->club_note ?? ''),
            TextColumn::make('real_start')
                ->label('Start v')
                ->dateTime('H:i')
                ->placeholder('—'),
            TextColumn::make('rent_si')
                ->label('Půjčit čip'),
            TextColumn::make('entry_stages')
                ->badge()
                ->separator(',')
                ->label('Etapy')
                ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                ->searchable(),
            TextColumn::make('entry_status')
                ->label('Stav přihlášky')
                ->badge()
                ->searchable(),
            TextColumn::make('created_at')
                ->label('Vytvořeno')
                ->date('d.m.Y')
                ->description(fn (UserEntry $record): string => $record->created_at?->format('H:i') ?? '')
                ->searchable()
                ->sortable(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table->recordClasses('!py-0');
    }

    public function getTableRecordsPerPage(): ?int
    {
        return 50;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('entry_status')
                ->options(EntryStatus::enumArray())->multiple()
                ->default([EntryStatus::Create->value, EntryStatus::Edit->value]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionAction::make('delete_user_entry')
                ->hidden(fn (UserEntry $record): bool => $this->hideDeleteAction($record))
                ->action(function (UserEntry $record): void {

                    $deletedRaceProfile = $record->userRaceProfile->user_race_full_name;

                    /* @description  Delete from Oris Entry */
                    if (EmptyType::intNotEmpty($record->oris_entry_id)) {
                        $eventOrisId = $record->sportEvent->oris_id;

                        $orisResponse = (new \App\Services\SportEvents\Entries\OrisEntryClient())
                            ->deleteEntry((int) $record->oris_entry_id);

                        if ($orisResponse->Status === 'OK') {

                            $record->entry_status = EntryStatus::Cancel;
                            $record->saveOrFail();
                            $this->releaseRelaySlot($record);

                            Notification::make()
                                ->title('Úspěšně jsme odhlásili '.$deletedRaceProfile.'ze závodu')
                                ->body('Odhlášku doporučujeme zkontrolovat na ORISu.')
                                ->actions([
                                    ActionAction::make('view')
                                        ->label('Přejít na url závodu')
                                        ->button()
                                        ->openUrlInNewTab()
                                        ->url(OrisApiService::ORIS_URL.'/Zavod?id='.$eventOrisId),
                                ])
                                ->seconds(15)
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Něco se nepovedlo')
                                ->body('Toto pošli správci: '.$orisResponse->Status)
                                ->warning()
                                ->send();
                        }
                    } else {
                        /** @description Delete NonORIS entry */
                        $record->entry_status = EntryStatus::Cancel;
                        $record->saveOrFail();
                        $this->releaseRelaySlot($record);

                        Notification::make()
                            ->title('Úspěšně jsme odhlásili '.$deletedRaceProfile.'ze závodu')
                            ->body('Odhlášku proběhlo pouze v našem systému.')
                            ->warning()
                            ->send();
                    }
                })
                ->color(fn (UserEntry $userEntry): string => Auth::user()?->id === $userEntry->userRaceProfile->user?->id ? 'danger' : 'warning')
                ->label('Odhlásit')
                ->icon('heroicon-o-trash')
                ->disabled(fn (UserEntry $record): bool => AppHelper::allowModifyUserEntry($record->sportEvent))
                //->disabled(fn (UserEntry $record) => dd($record))
                //->modalHidden(fn (UserEntry $record): bool => $record->userRaceProfile->user() === auth()->user())

                ->modalHeading(fn (UserEntry $record): string => $record->userRaceProfile->user_race_full_name.' - odhlášení ze závodu')
                ->modalContent(view('filament.modals.user-cancel-entry'))
                ->modalDescription('Odhlášení proběhne pokud.')
                ->modalSubmitActionLabel('Odhlásit'),
        ];
    }

    private function getOrisEvent(bool $registerAll = false): ActionAction
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return ActionAction::make($registerAll ? 'createEventEntryFull' : 'createEventEntry')
            ->action(function (array $data): void {

                /** @var SportEvent $sportEvent */
                $sportEvent = $this->record;

                if ($sportEvent->isRelayDiscipline()) {
                    $userRaceProfile = UserRaceProfile::query()->where('id', '=', $data['raceProfileId'])->first();
                    $storeResult = $this->storeRelayUserEntry($sportEvent, $userRaceProfile, $data);

                    if ($storeResult) {
                        Notification::make()
                            ->title('Přihláška byla úspěšně vytvořena')
                            ->body('Přihláška byla provedena do interní relay sestavy.')
                            ->success()
                            ->seconds(8)
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Přihlášku se nepodařilo vytvořit')
                            ->body('Vybraný tým je pravděpodobně již obsazen nebo neexistuje.')
                            ->warning()
                            ->seconds(8)
                            ->send();
                    }
                } elseif ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
                    /**
                     * ORIS entry
                     * Part of ORIS enty
                     */
                    $userRaceProfile = UserRaceProfile::where('oris_id', '=', $data['raceProfileId'])->first();
                    $sportClass = SportClass::where('oris_id', '=', $data['classId'])->first();

                    $orisResponse = $this->orisCreateEntry($data, $userRaceProfile, $sportEvent);

                    if ($orisResponse->Status === 'OK') {

                    $entry = $this->storeUserEntry(true, $sportEvent, $userRaceProfile, $sportClass, $data, $orisResponse);

                    if ($entry !== null) {
                            Notification::make()
                                ->title('Přihláška  '.$userRaceProfile?->user_race_full_name.' do kategorie: '.$sportClass?->name)
                                ->body('Přihlášku si zkontroluj na stránkách závodu přímo v ORISu.')
                                ->success()
                                ->actions([
                                    ActionAction::make('view')
                                        ->label('Přejít na stránku závodu')
                                        ->button()
                                        ->openUrlInNewTab()
                                        ->url(OrisApiService::ORIS_URL.'/PrehledPrihlasenych?id='.$sportEvent->oris_id),
                                ])
                                ->seconds(15)
                                ->send();
                        }

                    } else {
                        Notification::make()
                            ->title('Přihláška  '.$userRaceProfile?->user_race_full_name.' do kategorie: '.$sportClass?->name)
                            ->body('Nebyla provedena. ORIS vrátil zprávu: '.$orisResponse->Status)
                            ->warning()
                            ->seconds(8)
                            ->send();
                    }
                } else {
                    /**
                     * Entry to NonORIS Event
                     */

                    /** @var ?UserRaceProfile $userRaceProfile */
                    $userRaceProfile = UserRaceProfile::query()->where('id', '=', $data['raceProfileId'])->first();
                    /** @var ?SportClass $sportClass */
                    $sportClass = SportClass::query()->where('id', '=', $data['classId'])->first();

                    $entry = $this->storeUserEntry(false, $sportEvent, $userRaceProfile, $sportClass, $data);

                    if ($entry !== null) {
                        Notification::make()
                            ->title('Přihláška  '.$userRaceProfile?->user_race_full_name.' do kategorie: '.$sportClass?->name)
                            ->body('Přihláška byla provedena pouze v interním systému')
                            ->success()
                            ->seconds(8)
                            ->send();
                    }
                }
            })

            ->disabled(
                function (SportEvent $sportEvent) use ($registerAll): bool {
                    if ($registerAll) {
                        if ($sportEvent->cancelled === true) {
                            return true;
                        }
                    } else {
                        if (
                            EmptyType::arrayEmpty((new UserRaceProfiles())->getUserRaceProfiles($this->record)->toArray())
                            || $sportEvent->cancelled
                            || ! Auth::user()?->canCreateEntry()
                        ) {
                            return true;
                        }
                    }

                    return false;
                }
            )

            ->color($registerAll ? 'gray' : 'primary')
            ->label($registerAll ? 'Přihlásit kohokoliv' : 'Přihlásit na závod')
            ->icon($registerAll ? 'heroicon-o-users' : 'heroicon-o-plus-circle')
            ->modalHeading('Přihlášení na závod')
            ->modalDescription('Vyber závodní profil, vyhledej vhodné kategorie a přihlas se.')
            ->modalSubmitActionLabel('Přihlásit')
            ->schema([
                Select::make('raceProfileId')
                    ->label('Vyberte závodní profil')
                    ->options(
                        $registerAll
                            ? (new UserRaceProfiles())->getUserRaceProfiles($this->record, true)
                            : (new UserRaceProfiles())->getUserRaceProfiles($this->record)
                    )
                    ->allowHtml()
                    ->default(function (SportEvent $sportEvent): ?int {
                        $userProfileRecords = (new UserRaceProfiles())->getUserRaceProfiles($this->record);
                        if ($sportEvent->oris_id === null && count($userProfileRecords) === 1) {
                            return (int) array_key_first($userProfileRecords->toArray());
                        } else {
                            return null;
                        }
                    })
                    ->required()
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(
                        (function (string $state, Set $set) {

                            /** @var SportEvent $sportEvent */
                            $sportEvent = $this->record;

                            if (! ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries)) {
                                return;
                            }

                            try {
                                $userProfile = UserRaceProfile::where('oris_id', '=', (int)$state)->first();

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
                                if (count($orisResponse) > 0) {
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
                        })
                    ),
                Select::make('classId')
                    ->label('Vyber kategorii')
                    ->options(function (callable $get) {
                        if ($this->record->oris_id !== null && $this->record->use_oris_for_entries) {
                            return $get('specific_response_class_id');
                        } else {
                            /** @var SportClass[] $eventClasses */
                            $eventClasses = SportClass::where('sport_event_id', '=', $this->record->id)
                                ->get();

                            $classes = [];
                            foreach ($eventClasses as $eventClass) {
                                $classDefinitionName = SportClassDefinition::where('id', '=', $eventClass->class_definition_id)->first();
                                $classes[$eventClass->id] = '<span class="font-medium">' . e($eventClass->name)
                                    . '</span> <span class="text-gray-400"> | '
                                    . e($classDefinitionName->classDefinitionFullLabel)
                                    . '</span>';
                            }

                            return $classes;
                        }
                    })
                    ->searchable()
                    ->allowHtml()
                    ->required(fn (): bool => ! $this->record->isRelayDiscipline())
                    ->visible(fn (): bool => ! $this->record->isRelayDiscipline())
                    ->loadingMessage('Nahrávám kategorie...'),
                Select::make('relayTeamMemberId')
                    ->label('Volné místo v týmu')
                    ->options(function (): Collection {
                        return $this->getAvailableRelayMemberSlots($this->record);
                    })
                    ->allowHtml()
                    ->searchable()
                    ->required(fn (): bool => $this->record->isRelayDiscipline())
                    ->visible(fn (): bool => $this->record->isRelayDiscipline()),

                Grid::make()->schema([
                    TextInput::make('si')
                        ->label('Číslo SI čipu')
                        ->numeric(),

                    ToggleButtons::make('rent_si')
                        ->label('Půjčit čip')
                        ->options([
                            0 => 'Ne',
                            1 => 'Ano ',
                        ])
                        ->default(0)
                        ->inline(),
                ])->columns(2),

                Grid::make()->schema([
                    Select::make('entry_stages')
                        ->label('Zvol etapy')
                        ->options(function () {
                            /** @var SportEvent $sportEvent */
                            $sportEvent = $this->record;
                            $options = [];
                            if ($sportEvent->stages !== null && $sportEvent->sport_id >= 1) {
                                $options = (new SportEventService())->getMultiEventStagesOptions($sportEvent);
                            }

                            return $options;
                        })
                        ->multiple()
                        ->default(function () {
                            /** @var SportEvent $sportEvent */
                            $sportEvent = $this->record;

                            return (new SportEventService())->getMultiEventDefaultOptions($sportEvent);
                        })
                        ->minItems(function () {
                            /** @var SportEvent $sportEvent */
                            $sportEvent = $this->record;

                            return ($sportEvent->stages === null || $sportEvent->stages === 0) ? 0 : 1;
                        })
                        ->required(function () {
                            /** @var SportEvent $sportEvent */
                            $sportEvent = $this->record;

                            return ! ($sportEvent->stages === null || $sportEvent->stages === 0);
                        })
                        ->visible(function () {
                            /** @var SportEvent $sportEvent */
                            $sportEvent = $this->record;

                            return ! ($sportEvent->stages === null || $sportEvent->stages === 0);
                        }),
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
                                    ->hint(function (): HtmlString {
                                        return new HtmlString('<a href="'.AppHelper::getPageHelpUrl('jak-se-prihlasit-na-oris-zavod.html').'" target="_blank">Prosím čtěte nápovědu.</a>');
                                    })
                                    ->hintColor('primary')
                                    ->hintIcon('heroicon-m-question-mark-circle')
                                    ->columnSpan([
                                        'sm' => 6,
                                        'xl' => 4,
                                    ]),
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
                                    ->afterStateUpdated(
                                        (function ($state, Set $set) {

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
                                    )->columnSpan([
                                        'sm' => 6,
                                        'xl' => 2,
                                    ]),
                            ])->columns(6),

                        ])->columns(2),
                    ])
                    ->collapsible()
                    ->persistCollapsed()
                    ->id('entry_additional_information'),

            ]);
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            //EventMap::make(['model' => $this->record]),
            //EventLinks::make(['model' => $this->record]),
        ];
    }

    private function orisCreateEntry(array $entryData, UserRaceProfile $userProfile, SportEvent $sportEvent): CreateEntry
    {
        return (new \App\Services\SportEvents\Entries\OrisEntryClient())->createEntry($entryData, $userProfile, $sportEvent);
    }

    /**
     * @description Show/Hide delete table Action
     */
    private function hideDeleteAction(UserEntry $userEntry): bool
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        if ((Auth::user()?->hasRole([
           AppRoles::EventMaster,
           AppRoles::EventOrganizer,
        ]))) {
            return false;
        }

        if ($userEntry->userRaceProfile !== null && UserRaceProfiles::allowAnotherUserCareTheirUserRaceProfile($userEntry->userRaceProfile)) {
            return false;
        }


        if ($userEntry->entry_status === EntryStatus::Cancel) {
            return true;
        }

        if ($userEntry->userRaceProfile?->user?->id !== Auth::user()?->id) {
            return true;
        }

        // TODO skryt kdy6 je po poslednim datu

        return false;
    }

    /**
     * @throws Throwable
     */
    private function storeUserEntry(
        bool $isOrisEvent,
        SportEvent $sportEvent,
        ?UserRaceProfile $userRaceProfile,
        ?SportClass $sportClass,
        array $data,
        ?CreateEntry $orisResponse = null
    ): ?UserEntry {
        return (new \App\Services\SportEvents\Entries\EntryPersister())->persist(
            $isOrisEvent, $sportEvent, $userRaceProfile, $sportClass, $data, $orisResponse
        );
    }

    private function storeRelayUserEntry(SportEvent $sportEvent, ?UserRaceProfile $userRaceProfile, array $data): bool
    {
        return (new \App\Services\SportEvents\Entries\RelaySlotManager())->reserveSlot(
            $sportEvent, $userRaceProfile, $data, new \App\Services\SportEvents\Entries\EntryPersister()
        );
    }

    private function releaseRelaySlot(UserEntry $userEntry): void
    {
        (new \App\Services\SportEvents\Entries\RelaySlotManager())->releaseSlot($userEntry);
    }

    /** @return Collection<int, string> */
    private function getAvailableRelayMemberSlots(SportEvent $sportEvent): Collection
    {
        return (new \App\Services\SportEvents\Entries\RelaySlotManager())->availableSlots($sportEvent);
    }
}
