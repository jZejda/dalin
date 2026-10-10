<?php

declare(strict_types=1);

namespace App\Mail;

use App\Mail\Concerns\ListsSportEventDeadlines;
use App\Filament\Clusters\Other\Pages\UserMailNotification;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Models\SportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Services\Mail\MailBranding;

class EventWeeklyEndsBySport extends Mailable
{
    use ListsSportEventDeadlines;
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    /**
     * @param Collection<int, SportEvent> $eventFirstDateEnd
     * @param Collection<int, SportEvent> $eventSecondDateEnd
     * @param Collection<int, SportEvent> $eventThirdDateEnd
     */
    public function __construct(
        private readonly Collection $eventFirstDateEnd,
        private readonly Collection $eventSecondDateEnd,
        private readonly Collection $eventThirdDateEnd,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: MailBranding::subject(__('mail/event-weekly-ends-by-sport.subject.event_weekly_ends_by_sport')),
        );
    }

    public function content(): Content
    {
        $terms = array_filter([
            __('mail/common.club_layout.terms.1') => $this->weeklyRows($this->eventFirstDateEnd, 'entry_date_1'),
            __('mail/common.club_layout.terms.2') => $this->weeklyRows($this->eventSecondDateEnd, 'entry_date_2'),
            __('mail/common.club_layout.terms.3') => $this->weeklyRows($this->eventThirdDateEnd, 'entry_date_3'),
        ]);

        $eventCount = $this->eventFirstDateEnd
            ->concat($this->eventSecondDateEnd)
            ->concat($this->eventThirdDateEnd)
            ->unique('id')
            ->count();

        return new Content(
            markdown: 'emails.event.eventWeeklyEndsBySport',
            with: [
                'terms' => $terms,
                'eventCount' => $eventCount,
                'from' => Carbon::now()->addDay()->format('j. n.'),
                'to' => Carbon::now()->addDays(8)->format('j. n.'),
                'eventsUrl' => SportEventResource::getUrl('index', panel: 'admin'),
                'settingsUrl' => UserMailNotification::getUrl(panel: 'admin'),
            ]
        );
    }


    /**
     * @param Collection<int, SportEvent> $events
     * @return list<array{day: string, month: string, name: string, url: string, meta: string, deadline: string}>
     */
    private function weeklyRows(Collection $events, string $deadlineColumn): array
    {
        return $this->deadlineRows(
            $events,
            $deadlineColumn,
            static fn (SportEvent $event): array => [$event->place, $event->sportDiscipline?->long_name],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
