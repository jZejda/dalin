<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCreditResource\Actions;

use App\Enums\AppRoles;
use App\Models\SportList;
use App\Models\SportRegion;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;

class AddUserTransportBillingModal
{
    public function getAction(): Action
    {
        return Action::make('addUserTransportBilling')
            ->action(function (array $data): void {

            })
            ->color('gray')
            ->label('Nastav cestovní vyúčtování')
            ->icon('heroicon-o-users')
            ->modalHeading('Přidej závod z ORISU')
            ->modalDescription('Přidá do systému zvolený závod s daty které aktuálně poskytuje ORIS')
            ->modalSubmitActionLabel('Přidej vyúčtování')
            ->visible(
                auth()->user() !== null
                    ? auth()->user()->hasRole([AppRoles::SuperAdmin, AppRoles::EventMaster])
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
                    ]),
            ]);

    }

}
