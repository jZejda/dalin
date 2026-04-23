<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\PreRaceSummaryMail;
use App\Models\SportEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPreRaceSummaryMailCommand extends Command
{
    protected $signature = 'mail:pre-race-summary
                            {event_id : ID race}
                            {email : Target e-mail address}';

    protected $description = 'Send pre-race-summary summary, [eventID] [emailAddress]';

    public function handle(): int
    {
        $eventId = (int) $this->argument('event_id');
        $email = (string) $this->argument('email');

        $event = SportEvent::with([
            'userEntry.userRaceProfile.user.userSetting',
            'userEntry.sportClassDefinition',
            'sportClasses',
            'sportEventNews' => fn ($q) => $q->orderByDesc('date')->limit(5),
            'sportEventLinks',
        ])->find($eventId);

        if ($event === null) {
            $this->error("Event ID {$eventId} was not found.");

            return self::FAILURE;
        }

        $entries = $event->userEntry;

        if ($entries->isEmpty()) {
            $this->warn("Race '{$event->name}' are no entries.");
        }

        Mail::to($email)->send(new PreRaceSummaryMail($event, $entries));

        $this->info("Email send {$email} for eventID: {$event->name}");

        return self::SUCCESS;
    }
}
