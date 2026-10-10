<?php

declare(strict_types=1);

namespace App\Mail;

use App\Filament\Clusters\Other\Pages\UserMailNotification;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Mail\Concerns\ListsSportEventDeadlines;
use App\Models\SportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Reminder of races whose first entry deadline ends in the member's chosen number of days.
 */
class EventEntryEnds extends Mailable
{
    use ListsSportEventDeadlines;
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    /**
     * @param Collection<int, SportEvent> $sportEventContent
     */
    public function __construct(
        private readonly Collection $sportEventContent,
        private readonly int $daysBefore,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('app.name').' - '.__('mail/event-entry-ends.subject.event_entry_ends'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event.sportEntryEnds',
            with: [
                'daysBefore' => $this->daysBefore,
                'events' => $this->deadlineRows(
                    $this->sportEventContent,
                    'entry_date_1',
                    static fn (SportEvent $event): array => [$event->sportDiscipline?->long_name, self::orisLabel($event)],
                ),
                'eventsUrl' => SportEventResource::getUrl('index', panel: 'admin'),
                'settingsUrl' => UserMailNotification::getUrl(panel: 'admin'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
