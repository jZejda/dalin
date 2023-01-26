<?php

namespace App\Http\Controllers\Cron;

use App\Http\Controllers\Controller;
use Log;

class CommonCron extends Controller
{


    public function run(): string
    {

        Log::channel('app')->info('update cron se spustuil');
        //return '';

        $updateEvent = new CronTabManager();
        $updateEvent->setDayOfWeek(6);


        $pokus = $updateEvent->getTaskCommandLine();



    }

}
