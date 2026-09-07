<?php

declare(strict_types=1);

use App\Enums\SportEventMarkerType;
use App\Enums\SportEventType;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use App\Models\SportList;
use App\Services\Map\MapMarkerResolver;
use Illuminate\Support\Facades\DB;

function createResolverTestEvent(array $overrides = []): SportEvent
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $sportList = SportList::create(['short_name' => 'MTBO', 'color' => '#2E7D32']);

    $event = SportEvent::factory()->create(array_merge([
        'sport_id' => $sportList->id,
        'discipline_id' => null,
        'event_type' => SportEventType::Race,
        'stages' => null,
    ], $overrides));

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    return $event->fresh(['sport', 'sportDiscipline']);
}

test('resolves sport icon and color from sport_lists for the main event pin', function () {
    $event = createResolverTestEvent();

    $visual = (new MapMarkerResolver())->resolveForEvent($event);

    expect($visual->iconSlug)->toBe('mtbo')
        ->and($visual->colorHex)->toBe('#2E7D32')
        ->and($visual->modifierLetter)->toBeNull()
        ->and($visual->categoryIconSlug)->toBeNull();
});

test('marks multi-stage races with the E modifier', function () {
    $event = createResolverTestEvent(['stages' => 3]);

    $visual = (new MapMarkerResolver())->resolveForEvent($event);

    expect($visual->modifierLetter)->toBe('E');
});

test('marks relay disciplines with the R modifier, taking precedence over stages', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    $discipline = SportDiscipline::create(['short_name' => 'ST', 'long_name' => 'Štafety', 'relays' => true]);
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $event = createResolverTestEvent(['discipline_id' => $discipline->id, 'stages' => 3]);

    $visual = (new MapMarkerResolver())->resolveForEvent($event);

    expect($visual->modifierLetter)->toBe('R');
});

test('maps event type to a category badge, race stays clean', function (SportEventType $type, ?string $expectedSlug) {
    $event = createResolverTestEvent(['event_type' => $type]);

    $visual = (new MapMarkerResolver())->resolveForEvent($event);

    expect($visual->categoryIconSlug)->toBe($expectedSlug);
})->with([
    'race' => [SportEventType::Race, null],
    'training' => [SportEventType::Training, 'training'],
    'training camp' => [SportEventType::TrainingCamp, 'training-camp'],
    'club championship' => [SportEventType::ClubChampionship, 'club-championship'],
    'other' => [SportEventType::Other, 'other'],
]);

test('auxiliary markers get a neutral icon and their own letter, not the sport visual', function () {
    $event = createResolverTestEvent();

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    $marker = SportEventMarker::factory()->ofType(SportEventMarkerType::Parking)->create([
        'sport_event_id' => $event->id,
        'letter' => 'P',
    ]);
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $visual = (new MapMarkerResolver())->resolveForMarker($marker, $event);

    expect($visual->iconSlug)->toBe('parking')
        ->and($visual->colorHex)->toBe('#616161')
        ->and($visual->modifierLetter)->toBe('P')
        ->and($visual->categoryIconSlug)->toBeNull();
});

test('bus stop markers get their own icon, not the sport visual', function () {
    $event = createResolverTestEvent();

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    $marker = SportEventMarker::factory()->ofType(SportEventMarkerType::BusStop)->create([
        'sport_event_id' => $event->id,
        'letter' => null,
    ]);
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $visual = (new MapMarkerResolver())->resolveForMarker($marker, $event);

    expect($visual->iconSlug)->toBe('bus-stop')
        ->and($visual->colorHex)->toBe('#616161');
});

test('markers representing the event centre reuse the event sport visual', function () {
    $event = createResolverTestEvent();

    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    $marker = SportEventMarker::factory()->ofType(SportEventMarkerType::ObRaceSimple)->create([
        'sport_event_id' => $event->id,
        'letter' => 'C',
    ]);
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $visual = (new MapMarkerResolver())->resolveForMarker($marker, $event);

    expect($visual->iconSlug)->toBe('mtbo')
        ->and($visual->colorHex)->toBe('#2E7D32');
});
