<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreditChange extends Mailable
{
    use Queueable;
    use SerializesModels;

    private User $user;
    private UserCredit $userCredit;

    public function __construct(User $user, UserCredit $userCredit)
    {
        $this->user = $user;
        $this->userCredit = $userCredit;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - ' . __('mail/user-credit-change.subject.userCreditChange'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.userCreditChange',
            with: [
                'user' => $this->user,
                'userCredit' => $this->userCredit,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
