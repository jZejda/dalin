<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BankTransaction;
use App\Services\Bank\BankAccountService;
use App\Services\Bank\MatchRules\ExtraMembershipFeesRule;

class TestController extends Controller
{
    public function test(): void
    {

        $transaction = BankTransaction::query()->where('id', '=', 106)->first();


        //dd($transaction);

        (new BankAccountService())->matchTransactionToUser($transaction, (new ExtraMembershipFeesRule())->getRule());



    }
}
