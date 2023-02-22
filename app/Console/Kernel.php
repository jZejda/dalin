<?php

namespace App\Console;

use App\Jobs\SendAddUpdateSportEventEmailJob;
use App\Jobs\SendNewPostsEmailJob;
use App\Jobs\SendSportEventEntryEndingEmailJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work --stop-when-empty')->everyFiveMinutes();

        // Mails
        // $schedule->job(new SendAddUpdateSportEventEmailJob())->dailyAt('7:00');
        $schedule->job(new SendNewPostsEmailJob())->everyThirtyMinutes();
        $schedule->job(new SendSportEventEntryEndingEmailJob())->everyThirtyMinutes();
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
