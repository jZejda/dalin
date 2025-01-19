<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserPasswordSend extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const string ACTION_SEND_PASSWORD = 'sendPassword';
    public const string ACTION_RESET_PASSWORD = 'resetPassword';

    private string $password;
    private User $user;
    private string $action;

    public function __construct(string $password, User $user, string $action = self::ACTION_SEND_PASSWORD)
    {
        $this->password = $password;
        $this->user = $user;
        $this->action = $action;
    }

    public function envelope(): Envelope
    {
        if ($this->action === self::ACTION_SEND_PASSWORD) {
            $actionSubject = 'Zaslání hesla k portálu';
        } else {
            $actionSubject = 'Reset hesla k portálu';
        }

        return new Envelope(
            subject: config('app.name') . ' - ' . $actionSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.userSendPassword',
            with: [
                'newPassword' => $this->password,
                'user' => $this->user,
                'action' => $this->action,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
