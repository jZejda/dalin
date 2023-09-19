<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class EntryEndsToPay extends Mailable
{
    use Queueable;

    private Collection $sportEvents;
    private int $deadline;

    /**
     * @param Collection $sportEvents
     * @param int $deadline
     */
    public function __construct(Collection $sportEvents, int $deadline)
    {
        $this->sportEvents = $sportEvents;
        $this->deadline = $deadline;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Zaslání platby k ' . $this->deadline . ' termínu závodů',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.entryEndsToPay',
            with: [
                'sportEvents' => $this->sportEvents,
                'deadline' => $this->deadline,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
