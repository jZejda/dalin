<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\EventEntryEnds;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserNotifySetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendSportEventEntryEndingEmailJob implements ShouldQueue
{
    use Dispatchable;use InteractsWithQueue;use Queueable;use SerializesModels;

    public function handle(): void
    {
        $hour = Carbon::now()->format('H');
        $mailNotifications = UserNotifySetting::where('options->sport_time_trigger', $hour)->get();

        if ($mailNotifications->isNotEmpty()) {
            /** @var UserNotifySetting $mailNotification */
            foreach ($mailNotifications as $mailNotification) {
                $user = User::where('id', '=', $mailNotification->user_id)->first();
                $options = $mailNotification->options['sport'];
                $daysBefore = $mailNotification->options['days_before_event_entry_ends'];

                $mailContent = SportEvent::wherein('sport_id', $options)
                    ->where('entry_date_1', '>', Carbon::now()->addDays($daysBefore))
                    ->where('entry_date_1', '<', Carbon::now()->addDays($daysBefore + 1))
                    ->get();

                if ($mailContent->isNotEmpty()) {
                    Mail::to($user)
                        ->queue(new EventEntryEnds($mailContent, $daysBefore));
                }
            }
        }
    }
}
