<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\ListsSportEventDeadlines;
use App\Models\SportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

/**
 * Report for billing specialists: races whose given entry deadline (1–3) is ending
 * right now and have entered members, so the entry fees can be paid.
 */
class EntryEndsToPay extends Mailable
{
    use ListsSportEventDeadlines;
    use Queueable;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    /**
     * @param Collection<int, SportEvent> $sportEvents
     */
    public function __construct(
        private readonly Collection $sportEvents,
        private readonly int $deadline,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' - '.__('mail/entry-ends-to-pay.subject.entry_ends_to_pay', ['term' => $this->termOrdinal()]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.entryEndsToPay',
            with: [
                'term' => $this->termOrdinal(),
                'termTitle' => __('mail/common.club_layout.terms.'.$this->deadline),
                'events' => $this->deadlineRows(
                    $this->sportEvents,
                    'entry_date_'.$this->deadline,
                    static fn (SportEvent $event): array => [self::orisLabel($event)],
                ),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function termOrdinal(): string
    {
        return __('mail/common.club_layout.term_ordinals.'.$this->deadline);
    }
}
