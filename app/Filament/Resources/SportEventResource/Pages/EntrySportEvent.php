<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Enums\AppRoles;
use App\Filament\Resources\SportEventResource\Pages\Actions\EntrySendMail;
use App\Filament\Resources\SportEventResource\Pages\Actions\EntryUpdateEvent;
use App\Filament\Resources\SportEventResource\Pages\Actions\Helpers\UserRaceProfiles;
use App\Http\Components\Oris\Response\CreateEntry;
use App\Models\SportClassDefinition;
use App\Http\Components\Oris\ManageEntry;
use App\Models\SportEvent;
use App\Shared\Helpers\EmptyType;
use App\Enums\EntryStatus;
use App\Http\Components\Oris\GuzzleClient;
use App\Models\SportClass;
use App\Models\User;
use App\Models\UserEntry;
use App\Filament\Resources\SportEventResource;
use App\Models\UserRaceProfile;
use App\Shared\Helpers\AppHelper;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Actions\Action as ActionAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Actions\Action as NotificationAction;
use App\Filament\Resources\SportEventResource\Service\SportEventService;
use Illuminate\Support\HtmlString;

class EntrySportEvent extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithRecord;

    public string|int|null|Model $record;

    protected static string $resource = SportEventResource::class;
    protected static string $view = 'filament.resources.sport-event-resource.pages.event-entry';

    public string $back_button_url = '/admin/sport-events';

    public function booted()
    {
        //$this->beforeBooted();

        // @todo refactor check Filament::user ability.
        if (!Auth::user()?->hasRole(User::ROLE_MEMBER . '|' . User::ROLE_EVENT_MASTER . '|' . User::ROLE_SUPER_ADMIN)) {
            $this->notify('warning', __('filament-shield::filament-shield.forbidden'));

            $this->beforeShieldRedirects();

            redirect($this->getShieldRedirectPath());

            return;
        }

        if (method_exists(parent::class, 'booted')) {
            parent::booted();
        }

        //$this->afterBooted();
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

        return 'Detail závodu - ' . $sportEvent->name;
    }

    protected function getHeaderActions(): array
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        $makeExportModal = new SportEventResource\Pages\Actions\ExportsData($sportEvent);
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

        if (!is_null($sportEvent->oris_id)) {
            $defaultActions[] = $updateEvent->showUpdateEventFromOris();
        }

        if (!is_null($registerAnyone)) {
            $defaultActions[] = $registerAnyone;
        }

        if (!is_null($sendEmail)) {
            $defaultActions[] = $sendEmail;
        }
        if (!is_null($makeExport)) {
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
                ->searchable()
                ->sortable(),
            TextColumn::make('userRaceProfile.UserRaceFullName')
                ->label('Registrace')
                ->badge()
                ->color(static function ($state): string {
                    if ($state === 'published') {
                        return 'success';
                    }
                    return 'gray';
                }),
            TextColumn::make('userRaceProfile.user.name')
                ->label('Uživatel')
                ->badge()
                ->color(static function ($state): string {
                    if ($state === auth()->user()?->name) {
                        return 'success';
                    }
                    return 'gray';
                })
                ->searchable(),
            TextColumn::make('note')
                ->label('Poznámka')
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->note ?? ''),
            TextColumn::make('club_note')
                ->label('Klubová poznámka')
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->club_note ?? ''),
            TextColumn::make('requested_start')
                ->label('Start v')
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->requested_start ?? ''),
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
                ->dateTime('d. m. Y - H:i')
                ->searchable()
                ->sortable(),
        ];
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
            TableAction::make('delete_user_entry')
                ->hidden(fn (UserEntry $record): bool => $this->hideDeleteAction($record))
                ->action(function (UserEntry $record): void {

                    $deletedRaceProfile = $record->userRaceProfile->user_race_full_name;

                    /* @description  Delete from Oris Entry */
                    if (EmptyType::intNotEmpty($record->oris_entry_id)) {
                        $eventOrisId = $record->sportEvent->oris_id;

                        $params = [
                            'entryid' => $record->oris_entry_id,
                        ];

                        $guzzleClient = new GuzzleClient();
                        $clientResponse = $guzzleClient->create()->request('POST', 'API', $guzzleClient->generateMultipartForm(GuzzleClient::METHOD_DELETE_ENTRY, $params));

                        $response = new ManageEntry();
                        $orisResponse = $response->data($clientResponse->getBody()->getContents());


                        //                    +Method: "deleteEntry"
                        //                    +Format: "json"
                        //                    +Status: "OK"
                        //                    +ExportCreated: "2023-03-08 23:31:58"
                        //                    +Data: null

                        if ($orisResponse->Status === 'OK') {

                            $record->entry_status = EntryStatus::Cancel;
                            $record->saveOrFail();

                            Notification::make()
                                ->title('Úspěšně jsme odhlásili ' . $deletedRaceProfile . 'ze závodu')
                                ->body('Odhlášku doporučujeme zkontrolovat na ORISu.')
                                ->actions([
                                    NotificationAction::make('view')
                                        ->label('Přejít na url závodu')
                                        ->button()
                                        ->openUrlInNewTab()
                                        ->url('https://oris.orientacnisporty.cz/Zavod?id=' . $eventOrisId),
                                ])
                                ->seconds(15)
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Neco se nepovedlo')
                                ->body('Toto pošli správci: ' .$orisResponse->Status)
                                ->warning()
                                ->send();
                        }
                    } else {
                        /** @description Delete NonORIS entry */
                        $record->entry_status = EntryStatus::Cancel;
                        $record->saveOrFail();

                        Notification::make()
                            ->title('Úspěšně jsme odhlásili ' . $deletedRaceProfile . 'ze závodu')
                            ->body('Odhlášku proběhlo pouze v našem systému.')
                            ->warning()
                            ->send();
                    }
                })
                ->color('danger')
                ->label('Odhlásit')
                ->icon('heroicon-o-trash')
                ->disabled(fn (UserEntry $record): bool => AppHelper::allowModifyUserEntry($record->sportEvent))
                //->disabled(fn (UserEntry $record) => dd($record))
                //->modalHidden(fn (UserEntry $record): bool => $record->userRaceProfile->user() === auth()->user())

                ->modalHeading(fn (UserEntry $record): string => $record->userRaceProfile->user_race_full_name . ' - odhlášení ze závodu')
                ->modalContent(view('filament.modals.user-cancel-entry'))
                ->modalDescription('Odhlšení proběhne pokud.')
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

                if ($sportEvent->oris_id !== null && $sportEvent->use_oris_for_entries) {
                    /**
                     * ORIS entry
                     * Part of ORIS enty
                     */
                    $userRaceProfile = UserRaceProfile::where('oris_id', '=', $data['raceProfileId'])->first();
                    $sportClass = SportClass::where('oris_id', '=', $data['classId'])->first();

                    $orisResponse = $this->orisCreateEntry($data, $userRaceProfile, $sportEvent);

                    if ($orisResponse->Status === 'OK') {

                        $storeResult = $this->storeUserEntry(true, $sportEvent, $userRaceProfile, $sportClass, $data, $orisResponse);

                        if ($storeResult) {
                            Notification::make()
                                ->title('Přihláška  ' . $userRaceProfile?->user_race_full_name . ' do kategorie: ' . $sportClass?->name)
                                ->body('Prihlášku si zkontroluj na stránkách závodu přímo v ORISu.')
                                ->success()
                                ->actions([
                                    NotificationAction::make('view')
                                        ->label('Přejít na url závodu')
                                        ->button()
                                        ->openUrlInNewTab()
                                        ->url('https://oris.orientacnisporty.cz/PrehledPrihlasenych?id=' . $sportEvent->oris_id),
                                ])
                                ->seconds(15)
                                ->send();
                        }

                    } else {
                        Notification::make()
                            ->title('Přihláška  ' . $userRaceProfile?->user_race_full_name . ' do kategorie: ' . $sportClass?->name)
                            ->body('Nebyla provedena. ORIS vrátil zprávu: ' . $orisResponse->Status)
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

                    $storeResult = $this->storeUserEntry(false, $sportEvent, $userRaceProfile, $sportClass, $data);

                    if ($storeResult) {
                        Notification::make()
                            ->title('Přihláška  ' . $userRaceProfile?->user_race_full_name . ' do kategorie: ' . $sportClass?->name)
                            ->body('Prihlášku byla provedena pouze v interním systému')
                            ->success()
                            ->seconds(8)
                            ->send();
                    }
                }
            })

            ->disabled(
                EmptyType::arrayEmpty((new UserRaceProfiles())->getUserRaceProfiles($this->record)->toArray())
                || $sportEvent->cancelled
                || !Auth::user()?->canCreateEntry()
            )

            ->color($registerAll ? 'gray' : 'primary')
            ->label($registerAll ? 'Přihlásit kohokoliv' : 'Přihlásit na závod')
            ->icon($registerAll ? 'heroicon-o-users' : 'heroicon-o-plus-circle')
            ->modalHeading('Přihlášení na závod')
            ->modalDescription('Vyber závodní profil, vyhledej vhodné kategorie a přihlas se.')
            ->modalSubmitActionLabel('Přihlásit')
            ->form([
                Select::make('raceProfileId')
                    ->label('Vyberte závodní profil')
                    ->options($registerAll ? (new UserRaceProfiles())->getUserRaceProfiles($this->record, true) : (new UserRaceProfiles())->getUserRaceProfiles($this->record))
                    ->required()
                    ->live()
                    ->afterStateUpdated(
                        (function ($state, Set $set) {

                            /** @var SportEvent $sportEvent*/
                            $sportEvent = $this->record;

                            try {
                                $userProfile = UserRaceProfile::where('oris_id', '=', $state)->first();

                                $params = [
                                    'format' => 'json',
                                    'method' => 'getValidClasses',
                                    'clubuser' => $userProfile->club_user_id ?? '',
                                    'comp' => $sportEvent->oris_id,
                                ];

                                $orisResponse = Http::get('https://oris.orientacnisporty.cz/API', $params)
                                    ->throw()
                                    ->json('Data');

                                if ($orisResponse === null) {
                                    Notification::make()
                                        ->title('Na závod není možné se uvedeným závodním profilem přihlásit')
                                        ->body('Překontrolujte zdali mát v závodním profilu vyplněno ORISID, dále zkontrolujte platnou registraci na daný tok,
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
                    ->label('Vyber kategorii orisu')
                    ->options(function (callable $get) {
                        if ($this->record->oris_id !== null && $this->record->use_oris_for_entries) {
                            return $get('specific_response_class_id');
                        } else {
                            $eventClasses = SportClass::where('sport_event_id', '=', $this->record->id)
                                ->get();

                            $classes = [];
                            foreach ($eventClasses as $eventClass) {
                                $classDefinitionName = SportClassDefinition::where('id', '=', $eventClass->class_definition_id)->first();
                                $classes[$eventClass->id] = $classDefinitionName->classDefinitionFullLabel;
                            }
                            return $classes;
                        }
                    })
                    ->searchable()
                    ->required()
                    ->loadingMessage('Loading authors...'),

                Grid::make()->schema([
                    TextInput::make('si')
                        ->label('Číslo SI čipu')
                        ->numeric(),

                    ToggleButtons::make('rent_si')
                        ->label('Půjčit čip')
                        ->options([
                            0 => 'Ne',
                            1 => 'Ano '
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
                            return !($sportEvent->stages === null || $sportEvent->stages === 0);
                        })
                        ->visible(function () {
                            /** @var SportEvent $sportEvent */
                            $sportEvent = $this->record;
                            return !($sportEvent->stages === null || $sportEvent->stages === 0);
                        }),
                ])->columns(1),

                Section::make('Ostatní informace')
                    ->description('Detailní informace k přihlášce doplň po rozkliknutí.')
                    ->schema([
                        Grid::make()->schema([
                            TextInput::make('note')
                                ->label('Poznámka')
                                ->hint('Poznámka pořadateli.'),
                            TextInput::make('club_note')
                                ->label('Klubová poznámka')
                                ->hint('Interní poznámka.'),

                            Grid::make()->schema([
                                TextInput::make('requested_start')
                                    ->label('Požadovaný start')
                                    ->hint(function (): HtmlString {
                                        return new HtmlString('<a href="seznam.cz" target="_blank">Prosím čtěte nápovědu.</a>');
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
                                        ]

                                    ])
                                    ->live()
                                    ->afterStateUpdated(
                                        (function ($state, Set $set) {

                                            $pattern = match($state) {
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
                    ->persistCollapsed(),

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
        $requiredParams = [
            'clubuser' => $userProfile->club_user_id ?? '',
            'class' => $entryData['classId'],
        ];

        $allOptionalParams = [
            'si' => $entryData['si'],
            'note' => $entryData['note'],
            'clubnote' => $entryData['club_note'],
            'rent_si' => $entryData['rent_si'],
            'requested_start' => $entryData['requested_start'],
        ];

        if (isset($entryData['entry_stages'])) {
            for ($stage = 1; $stage <= $sportEvent->stages; $stage++) {
                if(in_array('stage' . $stage, $entryData['entry_stages'])) {
                    $allOptionalParams['stage' . $stage] = '1';
                }

                /**
                 * @description
                 * else block is not necessary For ORIS is important if you send stageX with any value 1 or.
                 * the result is the same
                 * $allOptionalParams['stage' . $stage] = '0';
                 */
            }
        }

        $optionalParams = [];
        foreach ($allOptionalParams as $key => $value) {
            if (!is_null($value)) {
                $optionalParams[$key] = $value;
            }
        }

        $params = array_merge($requiredParams, $optionalParams);

        $guzzleClient = new GuzzleClient();
        $clientResponse = $guzzleClient->create()->request('POST', 'API', $guzzleClient->generateMultipartForm(GuzzleClient::METHOD_CREATE_ENTRY, $params));

        $response = new ManageEntry();

        return $response->data($clientResponse->getBody()->getContents());
        //dd($orisResponse->getStatus() === 'OK'); funguje cekni jestli jsi dostal OK
        //dd($orisResponse->getData()?->getEntry()->getID()); //funguje ID prihlasky
    }

    /**
     * @description Show/Hide delete table Action
     */
    private function hideDeleteAction(UserEntry $userEntry): bool
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        if ($userEntry->entry_status === EntryStatus::Cancel) {
            return true;
        }

        if ($userEntry->userRaceProfile->user->id !== auth()->user()?->id) {
            return true;
        }

        // TODO skryt kdy6 je po poslednim datu


        return false;
    }

    /**
     * @throws \Throwable
     */
    private function storeUserEntry(
        bool $isOrisEvent,
        SportEvent $sportEvent,
        UserRaceProfile $userRaceProfile,
        SportClass $sportClass,
        array $data,
        CreateEntry $orisResponse = null
    ): bool {
        $entry = new UserEntry();
        if ($isOrisEvent) {
            $entry->oris_entry_id = $orisResponse->Data?->Entry->ID;
        }
        $entry->sport_event_id = $sportEvent->id;
        $entry->user_race_profile_id = $userRaceProfile->id;
        $entry->class_definition_id = $sportClass->classDefinition?->id;
        $entry->class_name = $sportClass->name ?? 'N/A';
        $entry->note = $data['note'];
        $entry->club_note = $data['club_note'];
        $entry->requested_start = $data['requested_start'];
        $entry->si = $data['si'];
        $entry->rent_si = $data['rent_si'] ?? 0;
        $entry->entry_created = Carbon::now();
        $entry->entry_status = EntryStatus::Create;
        if (isset($data['entry_stages'])) {
            $entry->entry_stages = $data['entry_stages'];
        }

        if ($entry->saveOrFail()) {
            return true;
        }

        return false;
    }
}
