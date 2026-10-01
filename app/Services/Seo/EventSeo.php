<?php

declare(strict_types=1);

namespace App\Services\Seo;

use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * Search/social metadata of an event detail (/akce/{id}): a factual description
 * (type · date · place · discipline · organizers) and SportsEvent + BreadcrumbList JSON-LD.
 */
final class EventSeo
{
    /**
     * Only races are public competitions anyone can enter; trainings, camps and club
     * championships are member events, which Google's event guidelines leave out.
     */
    private const array SCHEMA_EVENT_TYPES = [SportEventType::Race];

    public function __construct(private readonly SiteSeo $siteSeo)
    {
    }

    public function dynamicData(SportEvent $event): SEOData
    {
        $url = route('sport-event.show', $event->id);
        $description = $this->description($event);

        $schema = SchemaCollection::make()
            ->add(fn (): array => $this->siteSeo->breadcrumbSchema([
                ['name' => __('sport-event.public.breadcrumb_home'), 'url' => url('/')],
                ['name' => $event->name, 'url' => $url],
            ]));

        $eventSchema = $this->eventSchema($event, $description, $url);
        if ($eventSchema !== null) {
            $schema->add(static fn (): array => $eventSchema);
        }

        return new SEOData(
            title: $event->name,
            description: $description,
            url: $url,
            schema: $schema,
        );
    }

    /**
     * "Zrušeno · Závod · 24.10.2026 · Brno, Líšeň · Krátká trať · PBM. <event info>"
     */
    public function description(SportEvent $event): ?string
    {
        $facts = array_filter([
            $event->cancelled ? __('sport-event.public.cancelled_badge') : null,
            $event->event_type !== null ? __('sport-event.type_enum.'.$event->event_type->value) : null,
            $this->dateRange($event),
            $this->placeName($event),
            $event->sportDiscipline?->long_name,
            Arr::join(array_filter((array) $event->organization, 'is_string'), ', '),
        ], static fn (mixed $fact): bool => is_string($fact) && trim($fact) !== '');

        $info = $this->siteSeo->plainText((string) $event->event_info);
        $text = implode(' · ', $facts);

        if ($info !== null) {
            $text = $text !== '' ? $text.'. '.$info : $info;
        }

        return $this->siteSeo->description(e($text));
    }

    /**
     * @return array<string, mixed>|null null when Google's required fields (date, location) are missing
     */
    private function eventSchema(SportEvent $event, ?string $description, string $url): ?array
    {
        $placeName = $this->placeName($event);

        if (! in_array($event->event_type, self::SCHEMA_EVENT_TYPES, true) || $event->date === null || $placeName === null) {
            return null;
        }

        $location = [
            '@type' => 'Place',
            'name' => $placeName,
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $placeName],
        ];

        if (is_numeric($event->gps_lat) && is_numeric($event->gps_lon)) {
            $location['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $event->gps_lat,
                'longitude' => (float) $event->gps_lon,
            ];
        }

        $organizers = array_map(
            static fn (string $club): array => ['@type' => 'SportsOrganization', 'name' => $club],
            array_values(array_filter((array) $event->organization, static fn (mixed $club): bool => is_string($club) && $club !== '')),
        );

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SportsEvent',
            'name' => $event->name,
            'url' => $url,
            'description' => $description,
            'sport' => 'Orienteering',
            'startDate' => $this->startDate($event),
            'endDate' => ($event->date_end !== null && $event->date_end->gt($event->date) ? $event->date_end : $event->date)->toDateString(),
            'eventStatus' => $event->cancelled ? 'https://schema.org/EventCancelled' : 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location' => $location,
            'organizer' => $organizers !== [] ? $organizers : null,
            'image' => $this->siteSeo->defaultImageUrl(),
            'sameAs' => $event->oris_id !== null ? OrisApiService::ORIS_URL.'/Zavod?id='.$event->oris_id : null,
        ], static fn (mixed $value): bool => $value !== null);
    }

    /**
     * Date with the start time when known (ORIS stores "10:30:00"), otherwise the date alone.
     */
    private function startDate(SportEvent $event): string
    {
        $date = $event->date;

        if ($date === null) {
            return '';
        }

        if (is_string($event->start_time) && preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $event->start_time) === 1) {
            return $date->copy()->setTimeFromTimeString($event->start_time)->toIso8601String();
        }

        return $date->toDateString();
    }

    private function dateRange(SportEvent $event): ?string
    {
        if ($event->date === null) {
            return null;
        }

        $range = $event->date->format(AppHelper::DATE_FORMAT);

        if ($event->date_end !== null && $event->date_end->gt($event->date)) {
            $range .= ' – '.$event->date_end->format(AppHelper::DATE_FORMAT);
        }

        return $range;
    }

    /**
     * ORIS sometimes appends coordinates to the place ("Harrachov, 50.7703N, 15.4311E").
     */
    public function placeName(SportEvent $event): ?string
    {
        $place = Str::squish((string) preg_replace(
            '/,\s*\d+(?:\.\d+)?\s*[NS]\s*,\s*\d+(?:\.\d+)?\s*[EW]\s*$/u',
            '',
            (string) $event->place,
        ));

        return $place !== '' ? $place : null;
    }
}
