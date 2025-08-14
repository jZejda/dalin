<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Cron\Jobs\UpdateBankTransaction;
use App\Models\BankTransaction;
use App\Models\UserSetting;
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
        $mailNotifications = UserSetting::query()
            ->whereJsonContains('options->news', '1')
            ->get();


        dd($mailNotifications);

    }
}
