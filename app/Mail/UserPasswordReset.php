<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class UserPasswordReset extends Mailable
{
    use Queueable;

    private string $password;
    private User $user;

    public function __construct(string $password, User $user)
    {
        $this->password = $password;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Reset hesla k portálu',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user.userResetPassword',
            with: [
                'newPassword' => $this->password,
                'user' => $this->user,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
