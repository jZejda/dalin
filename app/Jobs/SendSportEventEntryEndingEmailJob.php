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
        $hour = Carbon::now()->hour;

        Log::channel('site')->info(sprintf('E-mail notifikace SportEvent v %d hodin', $hour));

        // The trigger hour is stored as an int, older rows may hold a zero-padded string ("08"),
        // so compare in PHP rather than against the formatted 'H' string in SQL
        $mailNotifications = UserSetting::query()
            ->where('type', '=', UserSetting::TYPE_MAIL)
            ->get()
            ->filter(fn (UserSetting $setting): bool => is_numeric($setting->options['sport_time_trigger'] ?? null)
                && (int) $setting->options['sport_time_trigger'] === $hour);

        if ($mailNotifications->isNotEmpty()) {
            /** @var UserSetting $mailNotification */
            foreach ($mailNotifications as $mailNotification) {
                $user = User::query()
                    ->where('id', '=', $mailNotification->user_id)
                    ->where('active', '=', 1)
                    ->first();

                if (
                    $user !== null
                    &&
                    isset($mailNotification->options['sport'])
                    && isset($mailNotification->options['days_before_event_entry_ends'])
                ) {
                    $options = $mailNotification->options['sport'];
                    $daysBefore = $mailNotification->options['days_before_event_entry_ends'];

                    $mailContent = SportEvent::query()
                        ->with('sportDiscipline')
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
