<?php

namespace App\Filament\Resources\UserCreditResource\Widgets;

use App\Enums\UserCreditStatus;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserCreditStats extends BaseWidget
{
    public ?Model $record = null;

    protected function getCards(): array
    {
        return [
            Card::make('Nepřiřazené transakce Kč', $this->getSumUnAssignCredit()),
            Card::make('Počet nepřiřazených', $this->getCountUnAssignCredit()),
            Card::make('Pokus', $this->record),
        ];
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
