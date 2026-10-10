<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Mail\EventWeeklyEndsBySport;
use App\Models\SportEvent;
use App\Models\User;
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportEmailEventWeeklyEndsBySport implements CommonCronJobs
{
    public function run(): void
    {
        $users = User::query()
            ->where('active', '=', 1)
            ->get();

        /** @var User $user */
        foreach ($users as $user) {
            if (
                isset($user->getUserOptions()['week_report_by_sport'][0]) &&
                $user->getUserOptions()['week_report_by_sport'][0] === '1'
            ) {
                $sportIds = $user->getUserOptions()['week_report_by_sport'];
                $eventFirstDateEnd = $this->eventsWithDeadlineThisWeek($sportIds, 'entry_date_1');
                $eventSecondDateEnd = $this->eventsWithDeadlineThisWeek($sportIds, 'entry_date_2');
                $eventThirdDateEnd = $this->eventsWithDeadlineThisWeek($sportIds, 'entry_date_3');

                if ($eventFirstDateEnd->isNotEmpty() || $eventSecondDateEnd->isNotEmpty() || $eventThirdDateEnd->isNotEmpty()) {

                    Mail::to($user)
                        ->queue(new EventWeeklyEndsBySport($eventFirstDateEnd, $eventSecondDateEnd, $eventThirdDateEnd));
                    Log::channel('site')->info('MailWeeklyUserEventSummary mail for user: '.$user->email.' - '.$user->name);
                }
            }
        }
    }

    /**
     * @param array<int, string> $sportIds
     * @return Collection<int, SportEvent>
     */
    private function eventsWithDeadlineThisWeek(array $sportIds, string $deadlineColumn): Collection
    {
        return SportEvent::query()
            ->with('sportDiscipline')
            ->whereIn('sport_id', $sportIds)
            ->whereNotNull($deadlineColumn)
            ->where($deadlineColumn, '>=', Carbon::now()->addDay()->format(AppHelper::DB_DATE_TIME).' 00:00:00')
            ->where($deadlineColumn, '<=', Carbon::now()->addDays(8)->format(AppHelper::DB_DATE_TIME).' 23:59:59')
            ->orderBy($deadlineColumn)
            ->get();
    }
}
