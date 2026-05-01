<?php

declare(strict_types=1);

use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Services\IcalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

test('address method is called instead of addressName to emit LOCATION property', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    SportEvent::factory()->create([
        'place' => 'TestPlace',
        'event_type' => SportEventType::Race,
        'date' => Carbon::now()->addDay(),
        'cancelled' => false,
        'discipline_id' => null,
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $service = new IcalService();
    $events = $service->getEvents(SportEventType::Race);

    expect($events)->not->toBeEmpty();

    $found = false;
    foreach ($events as $event) {
        if (str_contains($event->toString(), 'LOCATION:TestPlace')) {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();
});

test('null coordinates do not produce GEO property in iCal output', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    SportEvent::factory()->create([
        'gps_lat' => null,
        'gps_lon' => null,
        'event_type' => SportEventType::Training,
        'date' => Carbon::now()->addDay(),
        'cancelled' => false,
        'discipline_id' => null,
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $service = new IcalService();
    $events = $service->getEvents(SportEventType::Training);

    expect($events)->not->toBeEmpty();

    foreach ($events as $event) {
        expect($event->toString())->not->toContain('GEO:0;0');
        expect($event->toString())->not->toContain('GEO:0.0;0.0');
    }
});

test('real coordinates produce GEO property in iCal output', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    SportEvent::factory()->create([
        'gps_lat' => '49.1952',
        'gps_lon' => '16.6068',
        'event_type' => SportEventType::Race,
        'date' => Carbon::now()->addDay(),
        'cancelled' => false,
        'discipline_id' => null,
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $service = new IcalService();
    $events = $service->getEvents(SportEventType::Race);

    expect($events)->not->toBeEmpty();

    $found = false;
    foreach ($events as $event) {
        if (str_contains($event->toString(), 'GEO:')) {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();
});

test('event includes URL property linking to event detail page', function () {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $sportEvent = SportEvent::factory()->create([
        'event_type' => SportEventType::Race,
        'date' => Carbon::now()->addDay(),
        'cancelled' => false,
        'discipline_id' => null,
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $service = new IcalService();
    $events = $service->getEvents(SportEventType::Race);

    expect($events)->not->toBeEmpty();

    $found = false;
    foreach ($events as $event) {
        if (str_contains($event->toString(), '/admin/sport-events/')) {
            $found = true;
            break;
        }
    }
    expect($found)->toBeTrue();
});
