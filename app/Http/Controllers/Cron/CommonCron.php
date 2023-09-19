<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Cron\Jobs\EntryEndsToPay;
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
        /** @description Wather updaterun at 08 and 17 hours */
        try {
            if ($this->runJob('weather_forecast')) {
                Log::channel('site')->info('START WeatherForecast run cron at ' . $this->getActualHour());
                (new UpdateEventWeather())->run();
                Log::channel('site')->info('STOP WeatherForecast run cron at ' . $this->getActualHour());
            }
        } catch (\Exception $e) {
            Log::channel('site')->warning('ERROR WeatherForecast:' . $e->getMessage());
        }

        /** @description Update Events */
        try {
            if ($this->runJob('site')) {
                Log::channel('WeatherForecast')->info('START EventUpdates run cron at ' . $this->getActualHour());
                (new UpdateEvent())->run();
                Log::channel('site')->info('STOP  EventUpdates run cron at ' . $this->getActualHour());
            }
        } catch (\Exception $e) {
            Log::channel('site')->warning('ERROR EventUpdates: ' . $e->getMessage());
        }

        /** @description Send Mail monthly user debit credit */
        try {
            if ($this->runJob('mail_monthly_user_debit_report')) {
                Log::channel('site')->info('START MailMonthlyUserDebitReport run cron at ' . $this->getActualHour());
                (new ReportEmailUserDebit())->run();
                Log::channel('site')->info('STOP  MailMonthlyUserDebitReport run cron at ' . $this->getActualHour());
            }
        } catch (\Exception $e) {
            Log::channel('site')->warning('ERROR MailMonthlyUserDebitReport: ' . $e->getMessage());
        }

        /** @description  Send Mail weekly sport event summary */
        try {
            if ($this->runJob('mail_weekly_user_event_summary')) {
                Log::channel('site')->info('START MailWeeklyUserEventSummary run cron at ' . $this->getActualHour());
                (new ReportEmailEventWeeklyEndsBySport())->run();
                Log::channel('site')->info('STOP  MailWeeklyUserEventSummary run cron at ' . $this->getActualHour());
            }
        } catch (\Exception $e) {
            Log::channel('site')->warning('ERROR ErrorMessage: ' . $e->getMessage());
        }

        /** @description Send Mail EntryEndsToPay */
        try {
            if ($this->runJob('mail_entry_ends_to_pay')) {
                Log::channel('site')->info('START MailEntryEndsToPay run cron at ' . $this->getActualHour());
                (new EntryEndsToPay())->run();
                Log::channel('site')->info('STOP  MailEntryEndsToPay run cron at ' . $this->getActualHour());
            }
        } catch (\Exception $e) {
            Log::channel('site')->warning('ERROR ErrorMessage: ' . $e->getMessage());
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

    private function runJob(string $jobTitle): bool
    {
        $runningJobPrefix = 'site-config.cron_hourly.' . $jobTitle . '.';
        $jobStatus = config($runningJobPrefix . 'active');
        $jobCheckRun = $this->checkRunningTime(
            config($runningJobPrefix . 'hours'),
            config($runningJobPrefix . 'days_in_month'),
            config($runningJobPrefix . 'months'),
            config($runningJobPrefix . 'days_in_week'),
        );

        if ($jobStatus && $jobCheckRun) {
            return true;
        }
        return false;
    }

}
