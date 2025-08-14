<?php

declare(strict_types=1);

namespace App\Filament\Resources\PostResource\Jobs;

use App\Mail\UserEntryNotification;
use App\Models\Post;
use App\Models\UserSetting;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsMail
{
    private Post $post;
    private string $subject;
    private int $selection;

    public function __construct(Post $post, string $subject, int $selection)
    {
        $this->post = $post;
        $this->subject = $subject;
        $this->selection = $selection;

    }

    public function send(): void
    {
        /** @var UserSetting[]|null $userSetting */
        $userSetting = null;

        if ($this->selection === 1) {
            $userSettings = UserSetting::query()
                ->whereJsonContains('options->news', '1')
                ->get();
        }

        if ($userSetting !== null) {
            foreach ($userSetting as $setting) {

                $user = DB::table('users')->where('id', '=', $setting->user_id)->first();

                Mail::to($user)
                    ->queue(new UserEntryNotification(
                        $this->sportEvent,
                        $this->subject,
                    ));

            }
            Log::channel('site')->info('E-mail notifikace');
        }
    }
}
