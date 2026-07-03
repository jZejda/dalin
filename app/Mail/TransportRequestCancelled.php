<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TransportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransportRequestCancelled extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly TransportRequest $transportRequest,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' | '.__('transport.mail.request_cancelled_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.transport.requestCancelled',
            with: [
                'transportRequest' => $this->transportRequest,
            ],
        );
    }
}
