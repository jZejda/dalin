<?php

declare(strict_types=1);

namespace App\Filament\Resources\PostResource\Jobs;

use App\Mail\NewPost;
use App\Models\Post;
use App\Models\User;
use App\Models\UserSetting;
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
        if ($this->selection === 1) {
            $userWithPrivateNotes = UserSetting::query()
                ->select('user_id')
                ->whereJsonContains('options->news', '1')
                ->get();

            $users = User::query()
                ->wherein('id', $userWithPrivateNotes->pluck('user_id')->toArray())
                ->where('active', '=', 1)
                ->get();
        } else {
            $users = User::query()
                ->where('active', '=', 1)
                ->get();
        }

        /** @var User[] $users */
        foreach ($users as $user) {
            Mail::to($user)->queue(new NewPost($this->post, $this->subject));

            Log::channel('site')->info('E-mail Novinka notifikace pro uzivatele ' . $user->name);
        }
    }
}
