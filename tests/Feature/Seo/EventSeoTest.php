<?php

declare(strict_types=1);

use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use App\Services\Seo\EventSeo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
    config(['site-config.club.full_name' => 'OK Testov', 'site-config.club.abbr' => 'TST']);
});

/**
 * @param  array<string, mixed>  $attributes
 */
function createSeoEvent(array $attributes = []): SportEvent
{
    return SportEvent::factory()->create(array_merge([
        'name' => '14. Jihomoravská liga',
        'event_type' => SportEventType::Race,
        'date' => Carbon::parse('2026-10-24'),
        'date_end' => null,
        'start_time' => '10:30:00',
        'place' => 'Brno, Líšeň',
        'gps_lat' => '49.2175',
        'gps_lon' => '16.6950',
        'organization' => ['PBM'],
        'discipline_id' => null,
        'level_id' => null,
        'oris_id' => 9864,
        'event_info' => null,
        'cancelled' => false,
    ], $attributes));
}

/**
 * @return array<string, array<string, mixed>>
 */
function eventJsonLd(string $html): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

    return collect($matches[1])
        ->map(static fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR))
        ->keyBy('@type')
        ->all();
}

it('renders a factual description and SportsEvent structured data for a race', function (): void {
    $event = createSeoEvent();

    $html = $this->get(route('sport-event.show', $event->id))
        ->assertOk()
        ->assertSee('<title>14. Jihomoravská liga | TST</title>', escape: false)
        ->assertSee('<meta name="description" content="'.__('sport-event.type_enum.race').' · 24.10.2026 · Brno, Líšeň · PBM">', escape: false)
        ->assertSee('<link rel="canonical" href="'.route('sport-event.show', $event->id).'">', escape: false)
        ->getContent();

    $schemas = eventJsonLd((string) $html);

    expect($schemas['SportsEvent'])
        ->toMatchArray([
            'name' => '14. Jihomoravská liga',
            'startDate' => Carbon::parse('2026-10-24 10:30:00')->toIso8601String(),
            'endDate' => '2026-10-24',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'organizer' => [['@type' => 'SportsOrganization', 'name' => 'PBM']],
            'sameAs' => OrisApiService::ORIS_URL.'/Zavod?id=9864',
        ])
        ->and($schemas['SportsEvent']['location'])->toMatchArray([
            'name' => 'Brno, Líšeň',
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Brno, Líšeň'],
            'geo' => ['@type' => 'GeoCoordinates', 'latitude' => 49.2175, 'longitude' => 16.695],
        ])
        ->and(array_column($schemas['BreadcrumbList']['itemListElement'], 'item'))
        ->toBe([url('/'), route('sport-event.show', $event->id)]);
});

it('marks a cancelled race in the description and the event status', function (): void {
    $event = createSeoEvent(['cancelled' => true]);

    $data = app(EventSeo::class)->dynamicData($event);
    $html = $this->get(route('sport-event.show', $event->id))->getContent();

    expect($data->description)->toStartWith(__('sport-event.public.cancelled_badge').' · ')
        ->and(eventJsonLd((string) $html)['SportsEvent']['eventStatus'])->toBe('https://schema.org/EventCancelled');
});

it('handles multi-day events without a start time and strips ORIS coordinates from the place', function (): void {
    $event = createSeoEvent([
        'date' => Carbon::parse('2026-10-23'),
        'date_end' => Carbon::parse('2026-10-25'),
        'start_time' => null,
        'place' => 'Harrachov, 50.7703N, 15.4311E',
    ]);

    $html = $this->get(route('sport-event.show', $event->id))
        ->assertSee('23.10.2026 – 25.10.2026 · Harrachov · PBM', escape: false)
        ->getContent();

    expect(eventJsonLd((string) $html)['SportsEvent'])->toMatchArray([
        'startDate' => '2026-10-23',
        'endDate' => '2026-10-25',
    ])->and(eventJsonLd((string) $html)['SportsEvent']['location']['name'])->toBe('Harrachov');
});

it('appends the event info as plain text and keeps the description short', function (): void {
    $event = createSeoEvent(['event_info' => '<p>Shromaždiště u <strong>hájovny</strong>.</p>'.str_repeat('<p>Parkování na louce.</p>', 20)]);

    $description = app(EventSeo::class)->description($event);

    expect($description)->toContain('PBM. Shromaždiště u hájovny. Parkování')
        ->toEndWith('…')
        ->and(mb_strlen((string) $description))->toBeLessThanOrEqual(160);
});

it('publishes no SportsEvent for member-only event types or events without a place', function (SportEventType $type, ?string $place): void {
    $event = createSeoEvent(['event_type' => $type, 'place' => $place]);

    $schemas = eventJsonLd((string) $this->get(route('sport-event.show', $event->id))->assertOk()->getContent());

    expect($schemas)->toHaveKey('BreadcrumbList')->not->toHaveKey('SportsEvent');
})->with([
    'training' => [SportEventType::Training, 'Brno, Líšeň'],
    'training camp' => [SportEventType::TrainingCamp, 'Jeseník'],
    'club championship' => [SportEventType::ClubChampionship, 'Brno'],
    'race without a place' => [SportEventType::Race, null],
]);
