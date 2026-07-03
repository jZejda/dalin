<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TransportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransportRequestCreated extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly TransportRequest $transportRequest,
        private readonly string $approveUrl,
        private readonly string $rejectUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' | '.__('transport.mail.request_created_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.transport.requestCreated',
            with: [
                'transportRequest' => $this->transportRequest,
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
            ],
        );
    }
}
