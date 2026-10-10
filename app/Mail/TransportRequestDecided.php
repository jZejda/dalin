<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\DescribesTransportRequest;
use App\Models\TransportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransportRequestDecided extends Mailable
{
    use DescribesTransportRequest;
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

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
        $request = $this->transportRequest;

        return new Content(
            markdown: 'emails.transport.requestDecided',
            with: [
                'approved' => $request->isApproved(),
                'eventName' => (string) $this->transportEvent($request)?->name,
                'eventDate' => $this->transportEventDate($request),
                'facts' => $this->filledFacts([
                    $this->driverFact($request),
                    $this->directionFact($request),
                    $this->seatsFact($request, __('transport.seats')),
                    $this->departureFact($request),
                ]),
                'transportUrl' => $this->transportPageUrl($request),
            ],
        );
    }
}
