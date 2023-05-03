<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\UserCredit;

class UserCreditObserver
{
    /**
         * Handle the UserCredit "updated" event.
         */
    public function updated(UserCredit $userCredit): void
    {
        //dd('dela to neco?');
    }
}
