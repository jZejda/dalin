<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use Closure;
use App\Filament\Resources\SportEventResource;
use App\Models\UserRaceProfile;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class EventEntry extends Page implements HasForms, HasTable
{
    use HasPageShield;

    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithRecord;

    protected static string $resource = SportEventResource::class;
    protected static string $view = 'filament.resources.sport-event-resource.pages.event-entry';


    public string $sportEventId = '';
    public string $orisClassId = '';

    public string $cancel_button_url = 'cancel-link';

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        static::authorizeResourceAccess();
        abort_unless(static::getResource()::canEdit($this->getRecord()), 403);
    }

    protected array $rules = [
        'oris_class_id' => 'required|min:3',
    ];

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
                ]),
        ];
    }

}
