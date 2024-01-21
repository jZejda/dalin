<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserCreditHeaderWidgets extends BaseWidget
{
    use HasWidgetShield;

    public ?Model $record = null;

    protected function getCards(): array
    {
        $usersAmountCount = DB::table('user_credits')
            ->where('user_id', '=', Auth()->user()?->id)
            ->select(['amount'])
            ->sum('amount');

        $usersAmountChartData = DB::table('user_credits')
            ->where('user_id', '=', Auth()->user()?->id)
            ->select(['amount', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return [
            Stat::make('Aktuální stav', $usersAmountCount . ' Kč')
                ->description($usersAmountCount >= 0 ? 'Hurá na závody' : 'Bylo by fajn zaslat dar')
                ->descriptionIcon($usersAmountCount >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart(Arr::pluck($usersAmountChartData, 'amount'))
                ->color($usersAmountCount >= 0 ? 'success' : 'danger'),
        ];
    }

    protected static ?string $maxHeight = '300px';

    public function getColumnSpan(): int | string | array
    {
        return 5;
    }
}
