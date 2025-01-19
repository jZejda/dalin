<?php

namespace App\Filament\Resources\UserCreditResource\Widgets;

use App\Enums\UserCreditStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserCreditStats extends BaseWidget
{
    public ?Model $record = null;

    protected function getCards(): array
    {
        return [
            Stat::make('Nepřiřazené transakce Kč', $this->getSumUnAssignCredit()),
            Stat::make('Počet nepřiřazených', $this->getCountUnAssignCredit()),
        ];
    }

    protected function getColumns(): int
    {
        return 2;
    }


    protected function getSumUnAssignCredit(): null|float
    {
        return DB::table('user_credits')->where('status', '=', UserCreditStatus::UnAssign->value)->sum('amount');
    }

    protected function getCountUnAssignCredit(): null|float
    {
        return DB::table('user_credits')->where('status', '=', UserCreditStatus::UnAssign->value)->count('id');
    }
}
