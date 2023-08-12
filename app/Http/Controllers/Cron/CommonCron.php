<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Cron\Jobs\ReportEmailEventWeeklyEndsBySport;
use App\Http\Controllers\Cron\Jobs\ReportEmailUserDebit;
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

        // Send Mail monthly user debit report - Firs day in month
        $emailUserDebitPrefix = 'site-config.cron_hourly.mail_monthly_user_debit_report.';
        $emailUserDebitStatus = config($emailUserDebitPrefix . 'active');
        $emailUserDebitCheckRun = $this->checkRunningTime(
            config($emailUserDebitPrefix . 'hours'),
            config($emailUserDebitPrefix . 'days_in_month'),
            config($emailUserDebitPrefix . 'months'),
            config($emailUserDebitPrefix . 'days_in_week'),
        );
        if ($emailUserDebitStatus && $emailUserDebitCheckRun) {
            (new ReportEmailUserDebit())->run();
        }

        // Send Mail weekly sport event summary
        $emailUserWeeklyReportPrefix = 'site-config.cron_hourly.mail_weekly_user_event_summary.';
        $emailUserWeeklyReportStatus = config($emailUserWeeklyReportPrefix . 'active');
        $emailUserWeeklyReportCheckRun = $this->checkRunningTime(
            config($emailUserWeeklyReportPrefix . 'hours'),
            config($emailUserWeeklyReportPrefix . 'days_in_month'),
            config($emailUserWeeklyReportPrefix . 'months'),
            config($emailUserWeeklyReportPrefix . 'days_in_week'),
        );
        if ($emailUserWeeklyReportStatus && $emailUserWeeklyReportCheckRun) {
            (new ReportEmailEventWeeklyEndsBySport())->run();
        }
    }

    private function getActualHour(): string
    {
        return Carbon::now()->format('H');
    }

    private function checkRunningTime(?array $hours, ?array $daysInMonth, ?array $months, ?array $daysInWeek): bool
    {
        $check = new CronTabManager(
            hours: $hours ?? [],
            daysInMonth: $daysInMonth ?? [],
            months: $months ?? [],
            daysInWeek: $daysInWeek ?? []
        );

        return $check->jobRunner();
    }

}
