<?php

declare(strict_types=1);

namespace App\Mail;

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
use Illuminate\Support\Str;

class EventWeeklyEndsBySport extends Mailable
{
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
            subject: config('app.name') . ' - ' . __('mail/event-weekly-ends-by-sport.subject.event_weekly_ends_by_sport'),
        );
    }

    public function content(): Content
    {
        $terms = array_filter([
            __('mail/event-weekly-ends-by-sport.club.first_term') => $this->eventRows($this->eventFirstDateEnd, 'entry_date_1'),
            __('mail/event-weekly-ends-by-sport.club.second_term') => $this->eventRows($this->eventSecondDateEnd, 'entry_date_2'),
            __('mail/event-weekly-ends-by-sport.club.third_term') => $this->eventRows($this->eventThirdDateEnd, 'entry_date_3'),
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
    private function eventRows(Collection $events, string $deadlineColumn): array
    {
        return array_values($events->filter(
            static fn (SportEvent $event): bool => $event->{$deadlineColumn} instanceof Carbon,
        )->map(function (SportEvent $event) use ($deadlineColumn): array {
            /** @var Carbon $deadline */
            $deadline = $event->{$deadlineColumn};
            $date = $event->date ?? $deadline;

            return [
                'day' => $date->format('j'),
                'month' => Str::upper($date->isoFormat('MMM')),
                'name' => $event->name,
                'url' => SportEventResource::getUrl('view', ['record' => $event], panel: 'admin'),
                'meta' => collect([$event->place, $event->sportDiscipline?->long_name])->filter()->implode(' · '),
                'deadline' => $deadline->format('j. n. · H:i'),
            ];
        })->all());
    }

    public function attachments(): array
    {
        return [];
    }
}
