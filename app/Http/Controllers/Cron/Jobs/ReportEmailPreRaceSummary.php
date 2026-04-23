<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Mail\PreRaceSummaryMail;
use App\Models\SportEvent;
use App\Models\UserEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportEmailPreRaceSummary implements CommonCronJobs
{
    private const int MAX_DAYS_BEFORE = 14;

    public function run(): void
    {
        for ($daysBefore = 1; $daysBefore <= self::MAX_DAYS_BEFORE; $daysBefore++) {
            $targetDate = now()->addDays($daysBefore)->toDateString();

            $events = SportEvent::whereDate('date', $targetDate)
                ->whereHas('userEntry')
                ->with([
                    'userEntry.userRaceProfile.user.userSetting',
                    'userEntry.sportClassDefinition',
                    'sportClasses',
                    'sportEventNews' => fn ($q) => $q->orderByDesc('date')->limit(5),
                    'sportEventLinks',
                ])
                ->get();

            foreach ($events as $event) {
                $entriesByUser = [];

                foreach ($event->userEntry as $entry) {
                    $userId = $entry->userRaceProfile?->user_id;
                    if ($userId === null) {
                        continue;
                    }
                    $entriesByUser[$userId][] = $entry;
                }

                foreach ($entriesByUser as $entries) {
                    /** @var UserEntry $firstEntry */
                    $firstEntry = $entries[0];
                    $user = $firstEntry->userRaceProfile?->user;

                    if ($user === null) {
                        continue;
                    }

                    $options = $user->getUserOptions();

                    if (!($options['pre_race_summary_enabled'] ?? false)) {
                        continue;
                    }
                    if ((int) ($options['pre_race_summary_days_before'] ?? 1) !== $daysBefore) {
                        continue;
                    }
                    if ((int) ($options['pre_race_summary_time_trigger'] ?? 17) !== now()->hour) {
                        continue;
                    }

                    $profile = $firstEntry->userRaceProfile;
                    $emailTo = ($profile !== null && $profile->email !== null) ? $profile->email : $user->email;

                    Mail::to($emailTo)->send(new PreRaceSummaryMail($event, collect($entries)));
                    Log::channel('site')->info('PreRaceSummary mail for user: ' . $user->email);
                }
            }
        }
    }
}
