<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Enums\EntryStatus;
use App\Http\Components\Oris\CreateEntry;
use App\Http\Components\Oris\GuzzleClient;
use App\Models\SportClass;
use App\Models\User;
use App\Models\UserEntry;
use Closure;
use App\Filament\Resources\SportEventResource;
use App\Models\UserRaceProfile;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action as ModalAction;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

    private function getOrisEvent(): ModalAction
    {
        return ModalAction::make('createEventEntry')
            ->action(function (array $data): void {

                $params = [
                    'clubuser' => $data['raceProfileId'],
                    'class' => $data['orisClassId'],
                ];

                $guzzleClient = new GuzzleClient();
                $clientResponse = $guzzleClient->create()->request('POST', 'API', $guzzleClient->generateMultipartForm(GuzzleClient::METHOD_CREATE_ENTRY, $params));

                $response = new CreateEntry();
                $orisResponse = $response->data($clientResponse->getBody()->getContents());

//                dd($orisResponse);
//                +Method: "createEntry"
//                +Format: "json"
//                +Status: "OK"
//                +ExportCreated: "2023-02-28 00:22:55"
//                +data: null


                //dd($orisResponse->getStatus() === 'OK'); funguje cekni jestli jsi dostal OK
                //dd($orisResponse->getData()?->getEntry()->getID()); //funguje ID prihlasky
                //dd($orisResponse->getExportCreated()); //cas prihlasky taky uloz


                if ($orisResponse->getStatus() === 'OK') {

                    $userProfileData = UserRaceProfile::where('oris_id', '=', $data['raceProfileId'])->first();
                    $category = SportClass::where('oris_id', '=', $data['orisClassId'])->first();

                    $entry = new UserEntry();
                    $entry->oris_entry_id = $orisResponse->getData()?->getEntry()->getID();
                    $entry->sport_event_id = $this->record->id;
                    $entry->user_race_profile_id = $userProfileData->id;
                    $entry->class_definition_id = $category->id;
                    $entry->note = null;
                    $entry->club_note = null;
                    $entry->requested_start = null;
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
                                    $orisResponse = Http::get(
                                        'https://oris.orientacnisporty.cz/API',
                                        [
                                            'format' => 'json',
                                            'method' => 'getValidClasses',
                                            'clubuser' => $state,
                                            'comp' => $this->record->oris_id,
                                        ]
                                    )
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

                                } catch (RequestException $e) {
                                    Filament::notify('danger', 'Nepodařilo se načíst data.');
                                    return;
                                }
                                Filament::notify('success', 'ORIS v pořádku vrátil požadovaná data.');

                                $set('oris_response_class_id', $selectData ?? []);
                            })
                    ),
                Select::make('orisClassId')
                    ->label('Vyber kategorii orisu')
                    ->options(function (callable $get) {
                        return $get('oris_response_class_id');
                    })
                    ->searchable()
                    ->required()
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
}
