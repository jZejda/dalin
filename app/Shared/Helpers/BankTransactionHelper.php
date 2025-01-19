<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

use App\Models\BankTransaction;

class BankTransactionHelper
{
    public static function hasTransactionUserCredit(BankTransaction $transaction): bool
    {
        return $transaction->userCredit()->exists();
    }

}
