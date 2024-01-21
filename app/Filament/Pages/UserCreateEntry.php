<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\UserRaceProfile;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class UserCreateEntry extends Page implements HasForms, HasTable
{
    use HasPageShield;

    use InteractsWithForms;
    use InteractsWithTable;

    public static ?int $navigationSort = 120;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.user-create-entry';
    protected static ?string $slug = 'user-create-entry';
    protected static ?string $navigationGroup = 'Správa';

    public string $sportEventId = '';
    public string $orisClassId = '';

    public string $cancel_button_url = 'cancel-link';

    protected array $rules = [
        'oris_class_id' => 'required|min:3',
    ];

    public function submit(): void
    {

        $this->form->getState();

        $formData = array_filter([
            'sportEventId' => $this->sportEventId,
            'orisClassId' => $this->orisClassId,
        ]);


        dd($formData);
        //        $orisResponse = Http::get(OrisApiService::ORIS_API_URL,
        //            [
        //                'format' => 'json',
        //                'method' => 'getEventEntries',
        //                'clubid' => 1,
        //                'eventid' => $eventEntry->oris_id,
        //            ])
        //            ->throw()
        //            ->object();

        //        /** @var UserRaceProfile $userRaceProfiles */
        //        $userRaceProfiles = DB::table('user_race_profiles')->get();


        //        if ($orisResponse->Status === 'OK') {
        //            foreach ($orisResponse->Data as $entry) {
        //
        //            }
        //        }

        // auth()->user()->update($state);

        $this->notify('success', 'Nacteno z ORISU OK.');
    }

    /**
     * Form
     * @return array
     */
    protected function getFormSchema(): array
    {
        return [
            Section::make('Zvolte závodní profil a vyberte vhodnou kategorii')
                ->columns(1)
                ->schema([
                    Select::make('sportEventId')
                        ->label('Vyber zavod/udalost')
                        ->options(UserRaceProfile::all()->pluck('user_race_full_name', 'oris_id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->suffixAction(
                            fn ($state, \Filament\Forms\Set $set) =>
                        Action::make('search_oris_category_by_oris_id')
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(function () use ($state, $set) {
                                if (blank($state)) {
                                    Notification::make()
                                        ->title('Chyby ve formuláři')
                                        ->body('Zvol závodní profil.')
                                        ->danger()
                                        ->seconds(10)
                                        ->send();
                                    return;
                                }

                                try {
                                    $orisResponse = Http::get(
                                        'https://oris.orientacnisporty.cz/API',
                                        [
                                            'format' => 'json',
                                            'method' => 'getValidClasses',
                                            'clubuser' => $state,
                                            'comp' => '7721',
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
                                    Notification::make()
                                        ->title('ORIS - Error')
                                        ->body('Nepodařilo se načíst data z ORISu.')
                                        ->danger()
                                        ->seconds(10)
                                        ->send();
                                    return;
                                }
                                Notification::make()
                                    ->title('ORIS - OK')
                                    ->body('ORIS v pořádku vrátil požadovaná data.')
                                    ->success()
                                    ->send();

                                $set('oris_response_class_id', $selectData);
                            })
                        ),
                    Select::make('orisClassId')
                        ->label('Vyber kategorii orisu')
                        ->options(function (callable $get) {
                            return $get('oris_response_class_id');
                        })
                        ->searchable()
                        ->required()
                ]),
        ];
    }

}
