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
use App\Services\Mail\MailBranding;

class TransportRequestCreated extends Mailable
{
    use DescribesTransportRequest;
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    public function __construct(
        private readonly TransportRequest $transportRequest,
        private readonly string $approveUrl,
        private readonly string $rejectUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: MailBranding::subject(__('transport.mail.request_created_subject')),
        );
    }

    public function content(): Content
    {
        $request = $this->transportRequest;

        return new Content(
            markdown: 'emails.transport.requestCreated',
            with: [
                'passenger' => (string) $request->user?->name,
                'seats' => $request->seats,
                'eventName' => (string) $this->transportEvent($request)?->name,
                'eventDate' => $this->transportEventDate($request),
                'note' => $request->note,
                'facts' => $this->filledFacts([
                    $this->directionFact($request),
                    $this->seatsFact($request),
                    $this->departureFact($request, 'departure_from_label'),
                    $this->vehicleFact($request),
                ]),
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
            ],
        );
    }
}
