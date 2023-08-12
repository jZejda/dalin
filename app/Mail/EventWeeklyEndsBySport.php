<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class EventWeeklyEndsBySport extends Mailable
{
    use Queueable;
    use SerializesModels;

    private Collection $sportEventContent;

    public function __construct(Collection $sportEventContent)
    {
        $this->sportEventContent = $sportEventContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Závody u kterých končí první termín přihlášek příští týden',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.eventWeeklyEndsBySport',
            with: [
                'sportEventContent' => $this->sportEventContent,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
