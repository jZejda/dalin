<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Cron\Jobs\UpdateEventWeather;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CommonCron extends Controller
{
    public function runHourly(): void
    {
        // Wather updaterun at 08 and 17 hours
        if ($this->getActualHour() === '08' || $this->getActualHour() === '17') {
            Log::channel('app')->info('Start run weather cron at ' . $this->getActualHour());
            (new UpdateEventWeather())->run();
            Log::channel('app')->info('Stop run weather cron at ' . $this->getActualHour());
        }
    }

    private function getActualHour(): string
    {
        return Carbon::now()->format('H');
    }

}
