<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\EventEntryEnds;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSportEventEntryEndingEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $hour = Carbon::now()->format('H');

        Log::channel('site')->info(sprintf('E-mail notifikace SportEvent v %d hodin', $hour));

        $mailNotifications = UserSetting::query()->where('options->sport_time_trigger', $hour)->get();

        if ($mailNotifications->isNotEmpty()) {
            /** @var UserSetting $mailNotification */
            foreach ($mailNotifications as $mailNotification) {
                $user = User::query()->where('id', '=', $mailNotification->user_id)->first();

                if (
                    isset($mailNotification->options['sport'])
                    && isset($mailNotification->options['days_before_event_entry_ends'])
                ) {
                    $options = $mailNotification->options['sport'];
                    $daysBefore = $mailNotification->options['days_before_event_entry_ends'];

                    $mailContent = SportEvent::query()
                        ->wherein('sport_id', $options)
                        ->where('entry_date_1', '>', Carbon::now()->addDays($daysBefore))
                        ->where('entry_date_1', '<', Carbon::now()->addDays($daysBefore + 1))
                        ->get();

                    if ($mailContent->isNotEmpty()) {
                        Mail::to($user)->queue(new EventEntryEnds($mailContent, $daysBefore));
                    }
                }
            }
        }
    }
}
