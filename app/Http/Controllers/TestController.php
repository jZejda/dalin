<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Cron\Jobs\UpdateBankTransaction;
use App\Models\BankTransaction;
use App\Services\Bank\BankAccountService;
use App\Services\Bank\MatchRules\ExtraMembershipFeesRule;
use App\Shared\Helpers\BankTransactionHelper;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function test(): void
    {

        //        dd(Carbon::now()->addDays(4));

        $sportEvents = DB::table('sport_events')
            ->where(function (Builder $query) {
                $query->where('last_update', '<', Carbon::now()->subDays(2))
                    ->orWhereNull('last_update');
            })
            ->where('date', '>', Carbon::now()->subDays(10))
            ->whereNotNull('oris_id')
            ->orderBy('date', 'asc')
            ->limit(15)
            ->get();


        dd($sportEvents);



        (new UpdateBankTransaction())->run();

        //        $transaction = BankTransaction::query()->where('id', '=', 103)->first();
        //
        //
        //        if (!BankTransactionHelper::hasTransactionUserCredit($transaction)) {
        //            (new BankAccountService())->matchTransactionToUser($transaction, (new ExtraMembershipFeesRule())->getRule());
        //        }

        //dd($transaction);





    }
}
