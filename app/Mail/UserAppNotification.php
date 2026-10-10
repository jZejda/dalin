<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

/**
 * A message written in the admin panel to members with the selected roles; the content is Markdown.
 */
class UserAppNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    private const string DATE_TIME_FORMAT = 'j. n. Y · H:i';

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    /** When the message was written, not when the queued mail is rendered. */
    private readonly string $sentAt;

    public function __construct(
        private readonly ?User $sender,
        private readonly string $userSubject,
        private readonly string $content,
        private readonly ?string $userReplyTo,
    ) {
        $this->sentAt = Carbon::now()->format(self::DATE_TIME_FORMAT);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: $this->userReplyTo !== null && trim($this->userReplyTo) !== '' ? [$this->userReplyTo] : [],
            subject: config('app.name').' | '.$this->userSubject
        );
    }

    public function content(): Content
    {
        $facts = array_values(array_filter([
            ['icon' => 'user', 'label' => __('mail/user-app-notification.club.sender_label'), 'value' => (string) $this->sender?->name],
            ['icon' => 'clock', 'label' => __('mail/user-app-notification.club.sent_at_label'), 'value' => $this->sentAt],
        ], static fn (array $fact): bool => $fact['value'] !== ''));

        return new Content(
            markdown: 'emails.event.userAppNotification',
            with: [
                'facts' => $facts,
                'content' => $this->content,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
