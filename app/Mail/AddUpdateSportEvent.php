<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AddUpdateSportEvent extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name') . ' - Změny v kalendáři závodů',
        );
    }

    public function content(): Content
    {
        // Note if any change in events Add or Update for last day
//        $sportEventsFirst = DB::table('sport_events')
//            ->whereBetween('entry_date_1', [Carbon::now()->addDay(), Carbon::now()->addDays(2)])
//            ->get();

        $sportEventsFirst = DB::table('sport_events')->get();

        return new Content(
            markdown: 'emails.event.addUpdateSportEvent',
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
