<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Cron\CommonCron;
use App\Mail\EventWeeklyEndsBySport;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserSetting;
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class TestController extends Controller
{
    public function test(): void
    {

        (new CommonCron())->runHourly();
    }
}
