<?php

declare(strict_types=1);

namespace App\Mail;

use App\Filament\Clusters\Other\Pages\UserMailNotification;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Services\OrisApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Services\Mail\MailBranding;

class PreRaceSummaryMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** Branded club layout, see resources/views/vendor/mail/html/themes/club.blade.php. */
    public $theme = 'club';

    /**
     * @param Collection<int, UserEntry> $entries
     */
    public function __construct(
        private readonly SportEvent $event,
        private readonly Collection $entries,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: MailBranding::subject(__('mail/pre-race-summary.subject.pre_race_summary', ['event' => $this->event->name])),
        );
    }

    public function content(): Content
    {
        $this->event->loadMissing(['sportClasses', 'sportDiscipline', 'sportEventLinks', 'sportEventNews']);

        return new Content(
            markdown: 'emails.event.preRaceSummary',
            with: [
                'event' => $this->event,
                'eyebrowDate' => $this->event->date?->isoFormat(__('mail/common.club_layout.day_month_format')),
                'facts' => $this->facts(),
                'runners' => $this->entries->map(fn (UserEntry $entry): array => $this->runner($entry))->values()->all(),
                'detailUrl' => SportEventResource::getUrl('view', ['record' => $this->event], panel: 'admin'),
                'orisUrl' => $this->event->oris_id !== null ? OrisApiService::ORIS_URL.'/Zavod?id='.$this->event->oris_id : null,
                'settingsUrl' => UserMailNotification::getUrl(panel: 'admin'),
            ]
        );
    }

    /**
     * @return list<array{icon: string, label: string, value: string}>
     */
    private function facts(): array
    {
        $event = $this->event;
        $facts = [];

        if ($event->date !== null) {
            $date = $event->date->isoFormat('LL');

            if ($event->date_end !== null && $event->date_end->ne($event->date)) {
                $days = (int) $event->date->diffInDays($event->date_end) + 1;
                $date = $event->date->format('j. n.').' – '.$event->date_end->format('j. n. Y')
                    .' ('.trans_choice('mail/pre-race-summary.body.days_count', $days, ['count' => $days]).')';
            }

            $facts[] = ['icon' => 'calendar-days', 'label' => __('mail/pre-race-summary.club.date_label'), 'value' => $date];
        }

        if (filled($event->start_time)) {
            $facts[] = ['icon' => 'flag', 'label' => __('mail/pre-race-summary.club.first_start_label'), 'value' => Str::substr($event->start_time, 0, 5)];
        }

        if (filled($event->place)) {
            $facts[] = ['icon' => 'map-pin', 'label' => __('mail/pre-race-summary.club.place_label'), 'value' => $event->place];
        }

        if (filled($event->sportDiscipline?->long_name)) {
            $facts[] = ['icon' => 'footprints', 'label' => __('mail/pre-race-summary.club.discipline_label'), 'value' => $event->sportDiscipline->long_name];
        }

        return $facts;
    }

    /**
     * @return array{name: string, detail: ?string, extra: ?string, lines: list<array{icon: string, text: string}>}
     */
    private function runner(UserEntry $entry): array
    {
        $profile = $entry->userRaceProfile;
        $lines = [];

        $start = $entry->requested_start ?? $entry->real_start?->format('H:i:s');

        if ($start !== null) {
            $startDisplay = Str::substr($start, 0, 5);
            $relative = $this->minutesFromFirstStart($start);

            $lines[] = [
                'icon' => 'timer',
                'text' => $relative !== null
                    ? __('mail/pre-race-summary.club.start_line', ['time' => $startDisplay, 'relative' => ($relative >= 0 ? '+' : '').$relative])
                    : $startDisplay,
            ];
        }

        /** @var SportClass|null $sportClass */
        $sportClass = $this->event->sportClasses->firstWhere('class_definition_id', $entry->class_definition_id);
        $course = collect([
            filled($sportClass?->distance) ? __('mail/pre-race-summary.club.course_distance', ['distance' => $sportClass->distance]) : null,
            filled($sportClass?->controls) ? trans_choice('mail/pre-race-summary.club.course_controls', (int) $sportClass->controls, ['count' => $sportClass->controls]) : null,
            filled($sportClass?->climbing) ? __('mail/pre-race-summary.club.course_climbing', ['climbing' => $sportClass->climbing]) : null,
        ])->filter()->implode(' · ');

        if ($course !== '') {
            $lines[] = ['icon' => 'route', 'text' => $course];
        }

        return [
            'name' => trim(($profile->first_name ?? '').' '.($profile->last_name ?? '')),
            'detail' => $entry->class_name,
            'extra' => $profile?->reg_number,
            'lines' => $lines,
        ];
    }

    private function minutesFromFirstStart(string $start): ?int
    {
        $entryMinutes = $this->toMinutes($start);
        $eventMinutes = $this->event->start_time !== null ? $this->toMinutes($this->event->start_time) : null;

        return $entryMinutes !== null && $eventMinutes !== null ? $entryMinutes - $eventMinutes : null;
    }

    private function toMinutes(string $time): ?int
    {
        $parts = explode(':', trim($time));

        return count($parts) >= 2 ? (int) $parts[0] * 60 + (int) $parts[1] : null;
    }

    public function attachments(): array
    {
        return [];
    }
}
