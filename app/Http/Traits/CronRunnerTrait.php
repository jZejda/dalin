<?php

namespace App\Http\Traits;


trait CronRunnerTrait {



    public function checkRun($cron_job)
    {

        // M H day(1-31) Mes. DenVtydnu(0-6 0=nedele)
        $cron_items = explode(' ', $cron_job);
        $c_min = $cron_items[0];
        $c_hour = $cron_items[1];
        $c_day = $cron_items[2];
        $c_month = $cron_items[3];
        $c_day_in_week = $cron_items[4];

        // Check minut
        if($c_min  == '*')
        {
            $min = true;
        }
        else
        {
            $c_min == date('i') ? $min = true : $min = false ;
        }

        // Check hour
        if($c_hour  == '*')
        {
            $hour = true;
        }
        else
        {
            $c_hour == date('H') ? $hour = true : $hour = false ;
        }

        // Check day
        if($c_day  == '*')
        {
            $day = true;
        }
        else
        {
            $c_day == date('d') ? $day = true : $day = false ;
        }

        // Check month
        if($c_month  == '*')
        {
            $month = true;
        }
        else
        {
            $c_month == date('m') ? $month = true : $month = false ;
        }

        // Check day in week
        if($c_day_in_week  == '*')
        {
            $day_in_week = true;
        }
        else
        {
            $c_day_in_week == date('w') ? $day_in_week = true : $day_in_week = false ;
        }



        if($min == true AND $hour == true AND $day == true AND $month == true AND $day_in_week == true)
        {
            return true;
        }
        else
        {
            return false;
        }


    }
}
