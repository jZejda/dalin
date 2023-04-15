<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\NewPosts;
use App\Models\Post;
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

class SendNewPostsEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        $hour = Carbon::now()->format('H');

        Log::channel('site')->info(sprintf('E-mail notifikace New Post v %d hodin', $hour));

        $mailNotifications = UserSetting::where('options->news_time_trigger', $hour)->get();

        if ($mailNotifications->isNotEmpty()) {
            /** @var UserSetting $mailNotification */
            foreach ($mailNotifications as $mailNotification) {
                $user = User::where('id', '=', $mailNotification->user_id)->first();
                $options = $mailNotification->options['news'];

                $mailContent = Post::whereIn('private', $options)
                    ->where('created_at', '>', Carbon::now()->subDays(2))
                    ->get();

                if ($mailContent->isNotEmpty()) {
                    Mail::to($user)
                        ->queue(new NewPosts($mailContent));
                }
            }
        }
    }
}
