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
use Illuminate\Support\Facades\Cache;
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
        $now = Carbon::now();

        Log::channel('site')->info(sprintf('E-mail notifikace New Post v %d hodin', $now->hour));

        // Each post falls into exactly one daily digest: the 24 hours before the trigger hour.
        $windowEnd = $now->copy()->startOfHour();
        $windowStart = $windowEnd->copy()->subDay();

        $mailSettings = UserSetting::query()
            ->where('type', '=', UserSetting::TYPE_MAIL)
            ->get();

        foreach ($mailSettings as $mailSetting) {
            $postStatuses = self::subscribedPostStatuses($mailSetting, $now->hour);

            if ($postStatuses === []) {
                continue;
            }

            $user = User::query()
                ->where('id', '=', $mailSetting->user_id)
                ->where('active', '=', 1)
                ->first();

            if ($user === null) {
                continue;
            }

            $mailContent = Post::query()
                ->whereIn('private', $postStatuses)
                ->where('created_at', '>=', $windowStart)
                ->where('created_at', '<', $windowEnd)
                ->get();

            if ($mailContent->isEmpty()) {
                continue;
            }

            // The scheduler URL may be hit more than once within the trigger hour
            if (!Cache::add(sprintf('mail:new-posts:%d:%s', $user->id, $now->toDateString()), true, $now->copy()->addDays(2))) {
                continue;
            }

            Mail::to($user)->queue(new NewPosts($mailContent));
        }
    }

    /**
     * Post statuses (public/internal) the user subscribed to, or [] when their digest is not due this hour.
     *
     * @return array<int, mixed>
     */
    private static function subscribedPostStatuses(UserSetting $setting, int $hour): array
    {
        $trigger = $setting->options['news_time_trigger'] ?? null;
        $statuses = $setting->options['news'] ?? null;

        // The trigger hour is stored as an int, older rows may hold a zero-padded string ("08")
        if (!is_numeric($trigger) || (int) $trigger !== $hour || !is_array($statuses)) {
            return [];
        }

        return array_values($statuses);
    }
}
