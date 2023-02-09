<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use Closure;
use Filament\Forms;
use App\Filament\Resources\SportEventResource;
use App\Http\Controllers\Discord\DiscordWebhookHelper;
use App\Http\Controllers\Discord\RaceEventAddedNotification;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\SportRegion;
use App\Services\OrisApiService;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class ListSportEvents extends ListRecords
{
    protected static string $resource = SportEventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            $this->getOrisEvent(),
            $this->getNotifiAction()->tooltip('Umožní poslat ručně notifikaci na vybraný kanál.'),
        ];
    }

    public function openSettingsModal(): void
    {
        $this->dispatchBrowserEvent('open-settings-modal');
    }

    private function getOrisEvent(): Action
    {

        return Action::make('orisEvent')
            ->action(function (array $data): void {


               // dd($data['oris_id']));

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
            ->color('secondary')
            ->label('Přidej závod z ORISU')
            ->icon('heroicon-o-plus-circle')
            ->modalHeading('Přidej závod z ORISU')
            ->modalSubheading('Přidá do systému zvolený závod s daty které aktuálně poskytuje ORIS')
            ->modalButton('Přidej závod')
            ->form([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('sport_id')
                            ->label('Vyber typ sportu')
                            ->options(SportList::all()->pluck('short_name', 'id'))
                            ->required()
                            ->default(1)
                            ->searchable(),
                        Forms\Components\Select::make('oris_all')
                            ->label('Zobrazit i neoficiální závody')
                            ->options([
                                0 => 'Pouze oficiální',
                                1 => 'Všechny'
                            ])
                            ->required()
                            ->default(0),
                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                            ->searchable(),
                        Forms\Components\DatePicker::make('datefrom')
                            ->label('Datum od')
                            ->default(date('Y-m-d', strtotime('first day of january this year'))),

                        Grid::make()->schema([
                            Select::make('oris_id')
                                ->label('ORIS ID')
                                ->hint('Hledej podle kritérí na ORISu')
                                ->hintIcon('heroicon-s-exclamation')
                                ->required()
                                ->searchable()
                                ->options(function (callable $get) {
                                    return $get('oris_event_id');
                                })
                                ->suffixAction(
                                    action: fn ($state, Closure $set, callable $get) =>
                                    \Filament\Forms\Components\Actions\Action::make('get_event')
                                        ->icon('heroicon-o-search')
                                        ->action(function () use ($state, $set, $get) {
//                                        if (blank($state))
//                                        {
//                                            Filament::notify('danger', 'Zvol konkrétní závod.');
//                                            return;
//                                        }

                                            try {

                                                $dateFrom = explode(' ', $get('datefrom'));

                                                $baseUriParams = [
                                                    'format' => 'json',
                                                    'method' => 'getEventList',
                                                    'datefrom' => $dateFrom[0],
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
                                                Filament::notify('danger', 'Nepodařilo se načíst data.');
                                                return;
                                            }

                                            $orisEventData = [];
                                            foreach ($orisResponse as $event) {
                                                $orisEventData[$event['ID']] = $event['ID'] . ' - ' . $event['Date'] . ' - ' . $event['Org1']['Abbr'] . ' - ' . $event['Name'] . ' - ' . $event['Discipline']['NameCZ'];
                                            }

                                            $set('oris_event_id', $orisEventData);

                                        })
                                ),
                        ])->columns(1),
                    ]),
            ]);
    }

    private function getNotifiAction(): Action
    {
        return Action::make('updateAuthor')
                ->action(function (array $data): void {
                    // if notifikace na Discord
                    /** @var SportEvent $sportEvent */
                    $sportEvent = SportEvent::query()->where('id', '=', $data['sportEventId'])->first();
                    (new RaceEventAddedNotification($sportEvent, $data['notificationType']))->sendNotification();
                    Notification::make()
                        ->title('Notifikace odeslána')
                        ->body('Na zvolený kanál jsi zaslal notifikaci ke konkrétnímu závodu')
                        ->success()
                        ->seconds(8)
                        ->send();
                })
                ->color('secondary')
                ->label('Pošli notifikaci')
                ->icon('heroicon-s-paper-airplane')
                ->modalHeading('Pošli notifikaci k závodu/akci')
                ->modalSubheading('Notifikace je možná poslat do různých kanálů na objekty, jakékoliv objekty v listu')
                ->modalButton('Ano poslat notifikaci')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('sportEventId')
                                ->label('Závod/událost')
                                ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id'))
                                ->required()
                                ->columnSpan(2)
                                ->searchable(),
                            Forms\Components\Select::make('notificationType')
                                ->label('Typ upozornění')
                                ->options([
                                    DiscordWebhookHelper::CONTENT_STATUS_NEW => 'Nová událost',
                                    DiscordWebhookHelper::CONTENT_STATUS_UPDATE => 'Upravená událost'
                                ])
                                ->default(DiscordWebhookHelper::CONTENT_STATUS_NEW)
                                ->required(),
                            Forms\Components\Select::make('chanelId')
                                ->label('Kanál')
                                ->options([
                                    1 => 'Discord',
                                    2 => 'E-mail'
                                ])
                                ->default(1)
                                ->required(),
                        ]),

                ]);
    }

}
