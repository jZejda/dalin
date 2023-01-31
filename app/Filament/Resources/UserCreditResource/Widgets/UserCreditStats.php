<?php

namespace App\Filament\Resources\UserCreditResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class UserCreditStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Zbývá na kontě Kč', DB::table('user_credits')->sum('amount')),
            Card::make('Transakcí', DB::table('user_credits')->count('*')),
            Card::make('Průměrná cena akce', number_format(DB::table('user_credits')->avg('amount'), 2)),
        ];
    }
}
