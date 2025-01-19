<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BankTransaction;
use App\Services\Bank\BankAccountService;
use App\Services\Bank\MatchRules\ExtraMembershipFeesRule;
use App\Shared\Helpers\BankTransactionHelper;

class TestController extends Controller
{
    public function test(): void
    {

        $transaction = BankTransaction::query()->where('id', '=', 103)->first();


        if (!BankTransactionHelper::hasTransactionUserCredit($transaction)) {
            (new BankAccountService())->matchTransactionToUser($transaction, (new ExtraMembershipFeesRule())->getRule());
        }

        //dd($transaction);





    }
}
