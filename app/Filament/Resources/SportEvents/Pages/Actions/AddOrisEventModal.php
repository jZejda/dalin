<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Set;
use App\Enums\AppRoles;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\SportRegion;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
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

                $onlyUpdate = SportEvent::query()->where('oris_id', '=', $data['oris_id'])->first();


                $event = (new OrisApiService())->updateEvent(intval($data['oris_id']));
                if ($event) {

                    if ($onlyUpdate !== null) {
                        Notification::make()
                            ->title(__('sport-event.actions.add_oris_event.notification_title_updated', ['id' => intval($data['oris_id'])]))
                            ->body(__('sport-event.actions.add_oris_event.notification_body_updated'))
                            ->warning()
                            ->seconds(8)
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('sport-event.actions.add_oris_event.notification_title_created', ['id' => intval($data['oris_id'])]))
                            ->body(__('sport-event.actions.add_oris_event.notification_body_created'))
                            ->success()
                            ->seconds(8)
                            ->send();
                    }
                }
            })
            ->color('gray')
            ->label(__('sport-event.actions.add_oris_event.label'))
            ->icon('heroicon-o-plus-circle')
            ->modalHeading(__('sport-event.actions.add_oris_event.modal_heading'))
            ->modalDescription(__('sport-event.actions.add_oris_event.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.add_oris_event.modal_submit'))
            ->visible(
                auth()->user() !== null && auth()->user()->hasRole([AppRoles::SuperAdmin, AppRoles::EventMaster])
            )
            ->schema([
                Grid::make(2)
                    ->schema([
                        Select::make('sport_id')
                            ->label(__('sport-event.actions.add_oris_event.sport_type'))
                            ->options(SportList::all()->pluck('short_name', 'id'))
                            ->required()
                            ->default(1)
                            ->searchable(),
                        Select::make('oris_all')
                            ->label(__('sport-event.actions.add_oris_event.show_unofficial'))
                            ->options([
                                0 => __('sport-event.actions.add_oris_event.official_only'),
                                1 => __('sport-event.actions.add_oris_event.all'),
                            ])
                            ->required()
                            ->default(0),
                        Grid::make()->schema([
                            Select::make('region_id')
                                ->label(__('sport-event.actions.add_oris_event.region'))
                                ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                                ->searchable(),
                        ])->columns(1),
                        DatePicker::make('datefrom')
                            ->label(__('sport-event.actions.add_oris_event.date_from'))
                            ->default(Carbon::now()->format(AppHelper::DB_DATE_TIME)),
                        DatePicker::make('dateto')
                            ->label(__('sport-event.actions.add_oris_event.date_to'))
                            ->default(Carbon::now()->addMonths(6)->format(AppHelper::DB_DATE_TIME)),


                        Grid::make()->schema([
                            Select::make('oris_id')
                                ->label(__('sport-event.actions.add_oris_event.oris_id'))
                                ->hint(__('sport-event.actions.add_oris_event.oris_id_hint'))
                                ->hintIcon('heroicon-m-exclamation-triangle')
                                ->required()
                                ->searchable()
                                ->options(function (callable $get) {
                                    return $get('oris_event_id');
                                })
                                ->suffixAction(
                                    action: fn ($state, Set $set, callable $get) =>
                                    Action::make('get_event')
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

                                                $orisResponse = Http::get(OrisApiService::ORIS_API_URL, $baseUriParams)
                                                    ->throw()
                                                    ->json('Data');


                                            } catch (RequestException $e) {
                                                Notification::make()
                                                    ->title(__('sport-event.common.oris_fetch_error'))
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
