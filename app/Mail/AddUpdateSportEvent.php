<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AddUpdateSportEvent extends Mailable
{
    use Queueable;
    use SerializesModels;

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

        $user = DB::table('users')->find(1);

        if ($user->id === 1) {
            $sportEventsFirst = DB::table('sport_events')->where('id', '=', 1)->get();
        } else {
            $sportEventsFirst = DB::table('sport_events')->where('id', '=', 2)->get();
        }

        //$sportEventsFirst = DB::table('sport_events')->where('id')->get();

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
