<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use DB;
use App\Mail\EventWeeklyEndsBySport;
use App\Models\User;
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ReportEmailEventWeeklyEndsBySport implements CommonCronJobs
{
    public function run(): void
    {
        $users = User::all();

        /** @var User $user */
        foreach ($users as $user) {
            if (isset($user->getUserOptions()['week_report_by_sport']) && $user->getUserOptions()['week_report_by_sport'][0] === '1') {
                $eventFirstDateEnd = DB::table('sport_events')
                    ->wherein('sport_id', $user->getUserOptions()['week_report_by_sport'])
                    ->whereNotNull('entry_date_1')
                    ->where('entry_date_1', '>=', Carbon::now()->addDay()->format(AppHelper::DB_DATE_TIME) . ' 00:00:00')
                    ->where('entry_date_1', '<=', Carbon::now()->addDays(8)->format(AppHelper::DB_DATE_TIME) . ' 23:59:59')
                    ->orderBy('entry_date_1', 'asc')
                    ->get();

                $eventSecondDateEnd = DB::table('sport_events')
                    ->wherein('sport_id', $user->getUserOptions()['week_report_by_sport'])
                    ->whereNotNull('entry_date_2')
                    ->where('entry_date_2', '>=', Carbon::now()->addDay()->format(AppHelper::DB_DATE_TIME) . ' 00:00:00')
                    ->where('entry_date_2', '<=', Carbon::now()->addDays(8)->format(AppHelper::DB_DATE_TIME) . ' 23:59:59')
                    ->orderBy('entry_date_2', 'asc')
                    ->get();

                $eventThirdDateEnd = DB::table('sport_events')
                    ->wherein('sport_id', $user->getUserOptions()['week_report_by_sport'])
                    ->whereNotNull('entry_date_3')
                    ->where('entry_date_3', '>=', Carbon::now()->addDay()->format(AppHelper::DB_DATE_TIME) . ' 00:00:00')
                    ->where('entry_date_3', '<=', Carbon::now()->addDays(8)->format(AppHelper::DB_DATE_TIME) . ' 23:59:59')
                    ->orderBy('entry_date_3', 'asc')
                    ->get();

                if ($eventFirstDateEnd->isNotEmpty()) {
                    Mail::to($user)
                        ->send(new EventWeeklyEndsBySport($eventFirstDateEnd, $eventSecondDateEnd, $eventThirdDateEnd));
                }
            }
        }
        //Log::channel('site')->info(sprintf('E-mail notifikace SportEvent v %d hodin', $hour));
    }
}
