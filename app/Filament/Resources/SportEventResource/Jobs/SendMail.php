<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Jobs;

use App\Mail\UserEntryNotification;
use DB;
use App\Models\SportEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMail
{
    private SportEvent $sportEvent;
    private string $subject;
    private string $content;
    private string $replyTo;

    public function __construct(SportEvent $sportEvent, string $subject, string $content, string $replyTo)
    {
        $this->sportEvent = $sportEvent;
        $this->subject = $subject;
        $this->content = $content;
        $this->replyTo = $replyTo;
    }

    public function send(): void
    {
        $userEntries = DB::table('users as u')
            ->distinct()
            ->select(['u.id', 'u.email'])
            ->leftJoin('user_race_profiles AS urp', 'urp.user_id', '=', 'u.id')
            ->leftJoin('user_entries AS ue', 'ue.user_race_profile_id', '=', 'urp.id')
            ->leftJoin('sport_events AS se', 'se.id', '=', 'ue.sport_event_id')
            ->where('se.id', '=', $this->sportEvent->id)
            ->get();

        if (count($userEntries) > 0) {
            foreach ($userEntries as $user) {
                $user = DB::table('users')->where('id', '=', $user->id)->first();

                Mail::to($user)
                    ->queue(new UserEntryNotification(
                        $this->sportEvent,
                        $this->subject,
                        $this->content,
                        $this->replyTo,
                    ));

            }
            Log::channel('site')->info('E-mail notifikace');
        }
    }
}
