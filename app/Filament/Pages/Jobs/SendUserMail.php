<?php

declare(strict_types=1);

namespace App\Filament\Pages\Jobs;

use App\Mail\UserAppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class SendUserMail
{
    private string $subject;
    private string $content;
    private string $replyTo;
    private array $targetUsers;

    public function __construct(string $subject, string $content, string $replyTo, array $targetUsers)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->replyTo = $replyTo;
        $this->targetUsers = $targetUsers;
    }

    public function send(): void
    {
        $users = User::with('roles')->whereHas("roles", function ($q) {
            $q->whereIn('name', $this->targetUsers);
        })->get();

        if (count($users) > 0) {
            foreach ($users as $user) {
                Mail::to($user)
                    ->queue(new UserAppNotification(
                        $user,
                        $this->subject,
                        $this->content,
                        $this->replyTo,
                    ));

            }
            Log::channel('site')->info('E-mail Users notifikace');
        }
    }
}
