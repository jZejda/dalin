<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages\Actions;

use App\Enums\AppRoles;
use App\Models\SportList;
use App\Models\SportRegion;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\DatePicker;

class AddOrisEventModal
{
    public function getAction(): Action
    {
        return Action::make('addOrisEvent')
            ->action(function (array $data): void {

                $event = (new OrisApiService())->updateEvent(intval($data['oris_id']));
                if ($event) {
                    Notification::make()
                        ->title('Závod ID ' . intval($data['oris_id']) . ' byl vytvořen')
                        ->body('V systému byl založen nový závod s kategoriemi a dostupnými službami. Data jsou aktuální oproti ORISu.')
                        ->success()
                        ->seconds(8)
                        ->send();
                }
            })
            ->color('gray')
            ->label('Přidej závod z ORISU')
            ->icon('heroicon-o-plus-circle')
            ->modalHeading('Přidej závod z ORISU')
            ->modalDescription('Přidá do systému zvolený závod s daty které aktuálně poskytuje ORIS')
            ->modalSubmitActionLabel('Přidej závod')
            ->visible(
                auth()->user() !== null
                    ? auth()->user()?->hasRole([AppRoles::SuperAdmin, AppRoles::EventMaster])
                    : false
            )
            ->form([
                Grid::make(2)
                    ->schema([
                        Select::make('sport_id')
                            ->label('Vyber typ sportu')
                            ->options(SportList::all()->pluck('short_name', 'id'))
                            ->required()
                            ->default(1)
                            ->searchable(),
                        Select::make('oris_all')
                            ->label('Zobrazit i neoficiální závody')
                            ->options([
                                0 => 'Pouze oficiální',
                                1 => 'Všechny'
                            ])
                            ->required()
                            ->default(0),
                        Grid::make()->schema([
                            Select::make('region_id')
                                ->label('Region')
                                ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                                ->searchable(),
                        ])->columns(1),
                        DatePicker::make('datefrom')
                            ->label('Datum od')
                            ->default(Carbon::now()->format(AppHelper::DB_DATE_TIME)),
                        DatePicker::make('dateto')
                            ->label('Datum do')
                            ->default(Carbon::now()->addMonths(6)->format(AppHelper::DB_DATE_TIME)),


                        Grid::make()->schema([
                            Select::make('oris_id')
                                ->label('ORIS ID')
                                ->hint('Hledej podle kritérí na ORISu')
                                ->hintIcon('heroicon-m-exclamation-triangle')
                                ->required()
                                ->searchable()
                                ->options(function (callable $get) {
                                    return $get('oris_event_id');
                                })
                                ->suffixAction(
                                    action: fn ($state, Set $set, callable $get) =>
                                    \Filament\Forms\Components\Actions\Action::make('get_event')
                                        ->icon('heroicon-o-magnifying-glass')
                                        ->action(function () use ($state, $set, $get) {

                                            try {

                                                $dateFrom = explode(' ', $get('datefrom'));
                                                $dateTo = explode(' ', $get('dateto'));


                                                $baseUriParams = [
                                                    'format' => 'json',
                                                    'method' => 'getEventList',
                                                    'datefrom' => $dateFrom[0],
                                                    'dateto' => $dateTo[0],

                                                ];

                                                $params = [
                                                    'all' => $get('oris_all'),
                                                    'sport' => $get('sport_id'),
                                                    'rg' => $get('region_id'),
                                                ];

                                                foreach ($params as $key => $value) {
                                                    if (!is_null($value)) {
                                                        $baseUriParams[$key] = $value;
                                                    }
                                                }

                                                $orisResponse = Http::get('https://oris.orientacnisporty.cz/API', $baseUriParams)
                                                    ->throw()
                                                    ->json('Data');


                                            } catch (RequestException $e) {
                                                Notification::make()
                                                    ->title('Nepodařilo se načíst data.')
                                                    ->danger()
                                                    ->send();
                                                return;
                                            }

                                            $orisEventData = [];
                                            foreach ($orisResponse as $event) {
                                                $date = Carbon::parse($event['Date'])->format(AppHelper::DATE_FORMAT);
                                                $orisEventData[$event['ID']] = $event['ID'] . ' - ' . $date . ' - ' . $event['Org1']['Abbr'] . ' - ' . $event['Name'] . ' - ' . $event['Discipline']['NameCZ'];
                                            }

                                            $set('oris_event_id', $orisEventData);

                                        })
                                ),
                        ])->columns(1),
                    ]),
            ]);
    }
}
