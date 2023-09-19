<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Cron\CommonCron;
use App\Http\Controllers\Cron\Jobs\EntryEndsToPay;

class TestController extends Controller
{
    public function test(): void
    {

        (new EntryEndsToPay())->run();
    }
}
