<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\UserParamType;
use App\Models\UserCredit;
use Illuminate\Support\Facades\DB;

class UserCreditObserver
{
    /**
     * Handle the UserCredit "created" event.
     */
    public function created(UserCredit $userCredit): void
    {
        $this->updateUserBalance($userCredit);
    }

    /**
     * Handle the UserCredit "updated" event.
     */
    public function updated(UserCredit $userCredit): void
    {
        $this->updateUserBalance($userCredit);
    }

    private function updateUserBalance(UserCredit $userCredit): void
    {
        $usersAmountCount = DB::table('user_credits')
            ->where('user_id', '=', $userCredit->user_id)
            ->select(['amount'])
            ->sum('amount');

        $userCredit->user?->setParam(UserParamType::UserActualBalance, $usersAmountCount);
    }
}
