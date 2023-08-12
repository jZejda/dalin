<?php

declare(strict_types=1);

namespace App\Console;

use App\Jobs\SendNewPostsEmailJob;
use App\Jobs\SendSportEventEntryEndingEmailJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --stop-when-empty')->everyFiveMinutes();

        // Mails
        // $schedule->job(new SendAddUpdateSportEventEmailJob())->dailyAt('7:00');
        $schedule->job(new SendNewPostsEmailJob())->everyThirtyMinutes();
        $schedule->job(new SendSportEventEntryEndingEmailJob())->everyThirtyMinutes();


        // $schedule->job(new SportEventUpdateJob())->dailyAt('22:00');


        //        $schedule->call(function () {
        //            /** @var SportEvent[] $sportEvents */
        //            $sportEvents = DB::table('sport_events')
        //                ->where('last_update', '<', Carbon::now()->subDays(7))
        //                ->whereNotNull('oris_id')
        //                ->orWhereNull('last_update')
        //                ->where('date', '>', Carbon::now()->addDays(4))
        //                ->orderBy('date', 'asc')
        //                ->limit(5)
        //                ->get();
        //
        //            foreach ($sportEvents as $sportEvent) {
        //                $service = new OrisApiService();
        //                $service->updateEvent($sportEvent->oris_id, true);
        //                Log::channel('site')->info('CRON - Automatický update události ID: ' . $sportEvent->id . ' nazev: ' . $sportEvent->name);
        //            }
        //        })->dailyAt('22:00');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
