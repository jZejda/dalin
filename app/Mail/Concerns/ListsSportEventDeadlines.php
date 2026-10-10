<?php

declare(strict_types=1);

namespace App\Mail\Concerns;

use App\Filament\Resources\SportEvents\SportEventResource;
use App\Models\SportEvent;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Event rows with a date block and an entry deadline pill (x-mail::club.event),
 * shared by the club-layout mails that list races by their entry deadline.
 */
trait ListsSportEventDeadlines
{
    /**
     * @param Collection<int, SportEvent> $events
     * @param Closure(SportEvent): list<string|null> $metaParts
     * @return list<array{day: string, month: string, name: string, url: string, meta: string, deadline: string}>
     */
    protected function deadlineRows(Collection $events, string $deadlineColumn, Closure $metaParts): array
    {
        return array_values($events->filter(
            static fn (SportEvent $event): bool => $event->{$deadlineColumn} instanceof Carbon,
        )->map(static function (SportEvent $event) use ($deadlineColumn, $metaParts): array {
            /** @var Carbon $deadline */
            $deadline = $event->{$deadlineColumn};
            $date = $event->date ?? $deadline;

            return [
                'day' => $date->format('j'),
                'month' => Str::upper($date->isoFormat('MMM')),
                'name' => $event->name,
                'url' => SportEventResource::getUrl('view', ['record' => $event], panel: 'admin'),
                'meta' => collect($metaParts($event))->filter()->implode(' · '),
                'deadline' => $deadline->format('j. n. · H:i'),
            ];
        })->all());
    }

    protected static function orisLabel(SportEvent $event): ?string
    {
        return $event->oris_id !== null ? 'ORIS '.$event->oris_id : null;
    }
}
