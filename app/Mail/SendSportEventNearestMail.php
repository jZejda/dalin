<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SendSportEventNearestMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Blížící se konec přihlášek',
        );
    }

    public function content(): Content
    {
        // Note 2 days before entry ending
        $sportEventsFirst = DB::table('sport_events')
            ->whereBetween('entry_date_1', [Carbon::now()->addDay(), Carbon::now()->addDays(2)])
            ->get();

        return new Content(
            markdown: 'emails.event.nearest',
            with: [
                'url' => 'url',
                'sportEvents' => $sportEventsFirst,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
