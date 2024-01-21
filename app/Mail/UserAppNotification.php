<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use App\Shared\Helpers\EmptyType;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAppNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    private User $user;
    private string $userSubject;
    private string $content;
    private ?string $userReplyTo;

    public function __construct(User $user, string $userSubject, string $content, ?string $userReplyTo)
    {
        $this->user = $user;
        $this->userSubject = $userSubject;
        $this->content = $content;
        $this->userReplyTo = $userReplyTo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: EmptyType::stringNotEmpty($this->userReplyTo) ? [$this->userReplyTo] : [],
            subject: config('app.name') . ' | ' . $this->userSubject
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.userAppNotification',
            with: [
                'user' => $this->user,
                'content' => $this->content,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
