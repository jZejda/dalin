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

    private Collection $eventFirstDateEnd;
    private Collection $eventSecondDateEnd;
    private Collection $eventThirdDateEnd;

    public function __construct(Collection $eventFirstDateEnd, Collection $eventSecondDateEnd, Collection $eventThirdDateEnd)
    {
        $this->eventFirstDateEnd = $eventFirstDateEnd;
        $this->eventSecondDateEnd = $eventSecondDateEnd;
        $this->eventThirdDateEnd = $eventThirdDateEnd;
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
                'eventFirstDateEnd' => $this->eventFirstDateEnd->isNotEmpty() ? $this->eventFirstDateEnd : null,
                'eventSecondDateEnd' => $this->eventSecondDateEnd->isNotEmpty() ? $this->eventSecondDateEnd : null,
                'eventThirdDateEnd' => $this->eventThirdDateEnd->isNotEmpty() ? $this->eventThirdDateEnd : null,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
