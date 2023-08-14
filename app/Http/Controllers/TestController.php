<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        /** @var User $user */
        $users = User::all();

        foreach ($users as $user) {
            if (isset($user->getUserOptions()['week_report_by_sport'])) {

                /** @var Collection $mailContent */
                $mailContent = SportEvent::wherein('sport_id', $user->getUserOptions()['week_report_by_sport'])
                    ->whereNotNull('entry_date_1')
                    ->where('entry_date_1', '>=', Carbon::now()->addDay()->format(AppHelper::DB_DATE_TIME) . ' 00:00:00')
                    ->where('entry_date_1', '<=', Carbon::now()->addDays(7)->format(AppHelper::DB_DATE_TIME) . ' 23:59:59')
                    ->orderBy('entry_date_1', 'asc')
                    ->get();

                if ($mailContent->isNotEmpty()) {
                    Mail::to($user)
                        ->send(new EventWeeklyEndsBySport($mailContent));
                }
            }
        }

        //        /**
        //         * @var UserSetting $userSetting
        //         */
        //        $userSetting = $user->userSettings()->first();
        //        dd($userSetting->options);
    }
}
