<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class PreRaceSummaryMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly SportEvent $event,
        private readonly Collection $entries,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - ' . __('mail/pre-race-summary.subject.pre_race_summary', ['event' => $this->event->name]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.preRaceSummary',
            with: [
                'event' => $this->event,
                'entries' => $this->entries,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
