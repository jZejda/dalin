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

class TransportRequestCancelled extends Mailable
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
        return new Envelope(
            subject: config('app.name').' | '.__('transport.mail.request_cancelled_subject'),
        );
    }

    public function content(): Content
    {
        $request = $this->transportRequest;

        return new Content(
            markdown: 'emails.transport.requestCancelled',
            with: [
                'passenger' => (string) $request->user?->name,
                'seats' => $request->seats,
                'eventName' => (string) $this->transportEvent($request)?->name,
                'eventDate' => $this->transportEventDate($request),
                'facts' => $this->filledFacts([
                    $this->directionFact($request),
                    $this->seatsFact($request, __('transport.mail.request_cancelled_seats_label')),
                    $this->vehicleFact($request, __('transport.mail.request_created_club.vehicle_label')),
                ]),
                'transportUrl' => $this->transportPageUrl($request),
            ],
        );
    }
}
