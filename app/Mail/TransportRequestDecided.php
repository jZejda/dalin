<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\TransportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransportRequestDecided extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly TransportRequest $transportRequest,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->transportRequest->isApproved()
            ? __('transport.mail.request_approved_subject')
            : __('transport.mail.request_rejected_subject');

        return new Envelope(
            subject: config('app.name').' | '.$subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.transport.requestDecided',
            with: [
                'transportRequest' => $this->transportRequest,
            ],
        );
    }
}
