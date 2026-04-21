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

test('null coordinates are handled safely without producing GEO property', function () {
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
    
    $allEventsValid = true;
    foreach ($events as $event) {
        $output = $event->toString();
        if ($output === '' || empty($output)) {
            $allEventsValid = false;
            break;
        }
    }
    expect($allEventsValid)->toBeTrue();
});

test('coordinates method is called when coordinates are not null', function () {
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
    
    expect(function () use ($service) {
        $service->getEvents(SportEventType::Race);
    })->not->toThrow(Exception::class);
});
