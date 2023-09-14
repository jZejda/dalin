<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Cron\CommonCron;

class TestController extends Controller
{
    public function test(): void
    {

        (new CommonCron())->runHourly();
    }
}
