<?php

namespace App\Mail;

use App\Models\SportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendSportEventNearestMail extends Mailable
{
    use Queueable, SerializesModels;

    private SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Sport Event Neares Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.nearest',
            with: [
                'url' => $this->sportEvent->oris_id,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
