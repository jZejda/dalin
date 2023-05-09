<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Cron\Jobs\UpdateEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Cron\Jobs\UpdateEventWeather;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CommonCron extends Controller
{
    public function runHourly(): void
    {
        // Wather updaterun at 08 and 17 hours
        $updateForecastActive = config('site-config.cron_hourly.weather_forecast.active');
        $updateForecastHours = config('site-config.cron_hourly.weather_forecast.hours');
        if ($updateForecastActive && in_array($this->getActualHour(), $updateForecastHours)) {
            Log::channel('app')->info('Start run weather cron at ' . $this->getActualHour());
            (new UpdateEventWeather())->run();
            Log::channel('app')->info('Stop run weather cron at ' . $this->getActualHour());
        }

        // Update Events
        $updateEventActive = config('site-config.cron_hourly.event_update.active');
        $updateEventHours = config('site-config.cron_hourly.event_update.hours');
        if ($updateEventActive && in_array($this->getActualHour(), $updateEventHours)) {
            (new UpdateEvent())->run();
        }
    }

    private function getActualHour(): string
    {
        return Carbon::now()->format('H');
    }

}
