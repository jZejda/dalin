<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use Closure;
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
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action as ModalAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Actions\Action as NotificationAction;

class EntrySportEvent extends Page implements HasForms, HasTable
{
    use HasPageShield;

    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithRecord;

    protected static string $resource = SportEventResource::class;
    protected static string $view = 'filament.resources.sport-event-resource.pages.event-entry';

    public string $back_button_url = '/admin/sport-events';

    public function booted()
    {
        $this->beforeBooted();

        // @todo refactor check Filament::user ability.
        if (!Auth::user()->hasRole(User::ROLE_MEMBER . '|' . User::ROLE_EVENT_MASTER . '|' . User::ROLE_SUPER_ADMIN)) {
            $this->notify('warning', __('filament-shield::filament-shield.forbidden'));

            $this->beforeShieldRedirects();

            redirect($this->getShieldRedirectPath());

            return;
        }

        if (method_exists(parent::class, 'booted')) {
            parent::booted();
        }

        $this->afterBooted();
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected array $rules = [
        'oris_class_id' => 'required|min:3',
    ];

    protected function getActions(): array
    {
        return [
            $this->getOrisEvent(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return UserEntry::where('sport_event_id', '=', $this->record->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('sportClassDefinition.name')
                ->label('Kategorie')
                ->searchable()
                ->sortable(),
            BadgeColumn::make('userRaceProfile.UserRaceFullName')
                ->label('Registrace')
                ->color(static function ($state): string {
                    if ($state === 'published') {
                        return 'success';
                    }
                    return 'secondary';
                    }),
            BadgeColumn::make('userRaceProfile.user.name')
                ->label('Uživatel')
                ->color(static function ($state): string {
                    if ($state === \auth()->user()->name) {
                        return 'success';
                    }
                    return 'secondary';
                })
                ->searchable(),
            TextColumn::make('sportEvent.date')
                ->label('Start')
                ->dateTime('d. m. Y - H:i')
                ->searchable()
                ->sortable(),
            TextColumn::make('note')
                ->label('Poznámka'),
            TextColumn::make('club_note')
                ->label('Klubová poznámka'),
            TextColumn::make('requested_start')
                ->label('Start v'),
            TextColumn::make('rent_si')
                ->label('Půjčit čip'),
            TextColumn::make('stage_x')
                ->label('Etapa'),
            BadgeColumn::make('entry_status')
                ->enum([
                    'created' => 'Vytvořeno',
                    'edited' => 'Upraveno',
                    'deleted' => 'Smazáno',
                ])
                ->colors([
                    'success' => 'created',
                    'secondary' => 'edited',
                    'danger' => 'deleted',
                ])
                ->searchable()
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('entry_status')
                ->options([
                    'created' => 'Vytvořeno',
                    'edited' => 'Upraveno',
                    'deleted' => 'Smazáno',
                ])->multiple()
                ->default(['created', 'edited']),
        ];
    }


    protected function getTableActions(): array
    {
        return [
//            DeleteAction::make()
//                    ->before(function (DeleteAction $action) {
//                        if (true) {
//
//                            $action->halt();
//                        }
//                    })
//                ->color('primary')
//                ->modalHeading(fn (UserEntry $record): string => (string)$record->id),

            TableAction::make('delete_user_entry')
                ->hidden(fn (UserEntry $record): bool => $this->hideDeleteAction($record))
                ->action(function (UserEntry $record): void {

                    $deletedRaceProfile = $record->userRaceProfile->user_race_full_name;
                    $eventOrisId = $record->sportEvent->oris_id;

                    /* @description  Delete from Oris Entry */
                    if (EmptyType::intNotEmpty($record->oris_entry_id)) {
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

                        if ($orisResponse->getStatus() === 'OK') {

                            $record->entry_status = EntryStatus::Deleted;
                            $record->saveOrFail();

                            Notification::make()
                                ->title('Úspěšně jsme odhlásili ' . $deletedRaceProfile . 'ze závodu')
                                ->body('Odhlášku doporučujeme zkontrolovat na ORISu.')
                                ->actions([
                                    NotificationAction::make('view')
                                        ->button()
                                        ->openUrlInNewTab()
                                        ->url('https://oris.orientacnisporty.cz/Zavod?id=' . $eventOrisId),
                                    NotificationAction::make('undo')
                                        ->button()
                                        ->color('secondary'),
                                ])
                                ->send();
                        }
                    }


                })
                ->color('danger')
                ->label('Odhlásit')
                ->icon('heroicon-o-trash')
                //->modalHidden(fn (UserEntry $record): bool => $record->userRaceProfile->user() === auth()->user())

                ->modalHeading(fn (UserEntry $record): string => $record->userRaceProfile->user_race_full_name . ' - odhlášení ze závodu')
                ->modalContent(view('filament.modals.user-cancel-entry'))
                ->modalSubheading('Odhlšení proběhne pokud.')
                ->modalButton('Odhlásit'),
        ];
    }

    private function getOrisEvent(): ModalAction
    {
        return ModalAction::make('createEventEntry')
            ->action(function (array $data): void {

                $requiredParams = [
                    'clubuser' => $data['raceProfileId'],
                    'class' => $data['orisClassId'],
                ];

                $allOptionalParams = [
                    'si' => $data['si'],
                    'note' => $data['note'],
                    'clubnote' => $data['club_note'],
                    'rent_si' => $data['rent_si'],
                    'requested_start' => $data['requested_start'],
                ];

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
                $orisResponse = $response->data($clientResponse->getBody()->getContents());

//                dd($orisResponse);
//                +Method: "createEntry"
//                +Format: "json"
//                +Status: "OK"
//                +ExportCreated: "2023-02-28 00:22:55"
//                +data: null


                //dd($orisResponse->getStatus() === 'OK'); funguje cekni jestli jsi dostal OK
                //dd($orisResponse->getData()?->getEntry()->getID()); //funguje ID prihlasky
//                dd($orisResponse->getData());
//                dd($orisResponse->getExportCreated()); //cas prihlasky taky uloz


                if ($orisResponse->getStatus() === 'OK') {

                    $userProfileData = UserRaceProfile::where('oris_id', '=', $data['raceProfileId'])->first();
                    $category = SportClass::where('oris_id', '=', $data['orisClassId'])->first();

                    $entry = new UserEntry();
                    $entry->oris_entry_id = $orisResponse->getData()?->getEntry()->getID();
                    $entry->sport_event_id = $this->record->id;
                    $entry->user_race_profile_id = $userProfileData->id;
                    $entry->class_definition_id = $category->id;
                    $entry->note = $data['note'];
                    $entry->club_note = $data['club_note'];
                    $entry->requested_start = $data['requested_start'];
                    $entry->si = $data['si'];
                    $entry->rent_si = $data['rent_si'] ?? 0;
                    $entry->stage_x = null;
                    $entry->entry_created = Carbon::now()->toDateTimeString();;
                    $entry->entry_status = EntryStatus::Created;
                    $entry->saveOrFail();

                    Notification::make()
                        ->title('Přihláška  ' . $userProfileData?->user_race_full_name ?? 'N/A'  . ' do kategorie: ' . $category?->classDefinition->name ?? 'N/A')
                        ->body('Prihlášku si zkontroluj na stránkách závodu.')
                        ->success()
                        ->seconds(8)
                        ->send();
                }
            })
            ->color('secondary')
            ->label('Přihlásit na závod')
            ->icon('heroicon-o-plus-circle')
            ->modalHeading('Přihlášení na závod')
            ->modalSubheading('Vyber závodní profil, vyhledej vhodné kategorie a přihlas se.')
            ->modalButton('Přihlásit')
            ->form([
                Select::make('raceProfileId')
                    ->label('Vyber zavod/udalost')
                    ->options($this->getUserRaceProfiles())
                    ->disablePlaceholderSelection()
                    ->searchable()
//                    ->default(52722)
                    ->required()
                    ->reactive()
                    ->suffixAction(
                        fn ($state, Closure $set) =>
                        Action::make('search_oris_category_by_oris_id')
                            ->icon('heroicon-o-search')
                            ->action(function () use ($state, $set) {

                                if (blank($state))
                                {
                                    Filament::notify('danger', 'Zvol závodní profil.');
                                    return;
                                }

                                try {
                                    $params = [
                                        'format' => 'json',
                                        'method' => 'getValidClasses',
                                        'clubuser' => $state,
                                        'comp' => $this->record->oris_id,
                                    ];

                                    $orisResponse = Http::get('https://oris.orientacnisporty.cz/API', $params)
                                        ->throw()
                                        ->json('Data');

                                    // TODO pokud je to null tak hlaska nemuzet se prihlasit :-)
                                    //dd($orisResponse);

                                    $selectData = [];
                                    if (count($orisResponse) > 0) {
                                        foreach ($orisResponse as $class) {
                                            $selectData[$class['ID']] = $class['ClassDesc'];
                                        }
                                    }

                                    $userProfileSi = UserRaceProfile::where('oris_id', '=', $state)->get();

                                } catch (RequestException $e) {
                                    Filament::notify('danger', 'Nepodařilo se načíst data.');
                                    return;
                                }
                                Filament::notify('success', 'ORIS v pořádku vrátil požadovaná data.');

                                $set('oris_response_class_id', $selectData ?? []);
                                $set('si', $userProfileSi[0]?->si);
                            })
                    ),
                Select::make('orisClassId')
                    ->label('Vyber kategorii orisu')
                    ->options(function (callable $get) {
                        return $get('oris_response_class_id');
                    })
                    ->searchable()
                    ->required(),

                Grid::make()->schema([
                    TextInput::make('note')
                        ->label('Poznámka')
                        ->hint('Poznámka pořadateli.'),
                    TextInput::make('club_note')
                        ->label('Klubová poznámka')
                        ->hint('Interní poznámka.'),
                    TextInput::make('requested_start')
                        ->label('Požadovaný start')
                        ->hint('Prosím s rozmyslem.'),
                    TextInput::make('si')
                        ->label('Číslo SI čipu'),
                    Toggle::make('rent_si')
                        ->extraAttributes(['class' => 'mt-4'])
                        ->label('Půjčit čip')
                        ->onIcon('heroicon-s-check')
                        ->offIcon('heroicon-s-x')
                        ->default(false),
                ])->columns(2),
            ]);
    }

    public function getUserRaceProfiles(): Collection
    {
        /*
            54 => "ABM7805 - Jiří Zejda"
            52722 => "ABM1602 - David Zejda"
            dotaz na csechny profily, pak sestav pole z tech ktere nejsu prihlaseny
         */
        return UserRaceProfile::all()
            ->where('user_id', '=', auth()->user()->id)
            ->pluck('user_race_full_name', 'oris_id');


    }

    /**
     * @description Show/Hide delete table Action
     */
    private function hideDeleteAction(UserEntry $userEntry): bool
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        if ($userEntry->entry_status === EntryStatus::Deleted) {
            return true;
        }

        if ($userEntry->userRaceProfile->user->id !== auth()->user()?->id)
        {
            return true;
        }

        // TODO skryt kdy6 je po poslednim datu


        return false;
    }
}
