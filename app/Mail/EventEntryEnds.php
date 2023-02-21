<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class EventEntryEnds extends Mailable
{
    use Queueable;
    use SerializesModels;

    private Collection $sportEventContent;
    private int $daysBefore;

    public function __construct(Collection $sportEventContent, int $daysBefore)
    {
        $this->sportEventContent = $sportEventContent;
        $this->daysBefore = $daysBefore;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Blíží se konec přihlášek závodů',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.sportEntryEnds',
            with: [
                'sportEventContent' => $this->sportEventContent,
                'daysBefore' => $this->daysBefore
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
