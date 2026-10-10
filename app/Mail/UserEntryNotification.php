<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\Mail\MailBranding;

/**
 * A message written by an organiser to everyone entered for a race; the content is Markdown.
 */
class UserEntryNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    public function __construct(
        private readonly SportEvent $sportEvent,
        private readonly string $userSubject,
        private readonly string $content,
        private readonly ?string $userReplyTo,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: $this->userReplyTo !== null && trim($this->userReplyTo) !== '' ? [$this->userReplyTo] : [],
            subject: MailBranding::subject($this->userSubject)
        );
    }

    public function content(): Content
    {
        $event = $this->sportEvent;

        $facts = array_values(array_filter([
            ['icon' => 'calendar-days', 'label' => __('mail/user-entry-notification.club.date_label'), 'value' => (string) $event->date?->isoFormat('LL')],
            ['icon' => 'map-pin', 'label' => __('mail/user-entry-notification.club.place_label'), 'value' => (string) $event->place],
        ], static fn (array $fact): bool => $fact['value'] !== ''));

        return new Content(
            markdown: 'emails.event.eventNotification',
            with: [
                'eventTitle' => collect([$event->name, $event->alt_name])->filter()->implode(' · '),
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
