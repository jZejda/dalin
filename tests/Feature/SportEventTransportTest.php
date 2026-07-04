<?php

declare(strict_types=1);

use App\Enums\SportEventTransportType;
use App\Models\SportEvent;
use App\Models\SportList;

beforeEach(function (): void {
    $this->sportList = SportList::query()->create(['short_name' => 'OB']);
});

it('defaults transport type to self transport', function (): void {
    $sportEvent = SportEvent::factory()->create([
        'sport_id' => $this->sportList->id,
        'discipline_id' => null,
        'use_oris_for_entries' => false,
    ]);

    expect($sportEvent->refresh()->transport_type)->toBe(SportEventTransportType::SelfOnly);
});

it('casts transport type to enum', function (): void {
    $sportEvent = SportEvent::factory()->create([
        'sport_id' => $this->sportList->id,
        'discipline_id' => null,
        'use_oris_for_entries' => false,
        'transport_type' => SportEventTransportType::Combined,
    ]);

    expect($sportEvent->refresh()->transport_type)->toBe(SportEventTransportType::Combined);
});

it('allows club vehicles only for club and combined transport', function (): void {
    expect(SportEventTransportType::ClubOnly->allowsClubVehicles())->toBeTrue()
        ->and(SportEventTransportType::Combined->allowsClubVehicles())->toBeTrue()
        ->and(SportEventTransportType::SelfOnly->allowsClubVehicles())->toBeFalse()
        ->and(SportEventTransportType::None->allowsClubVehicles())->toBeFalse();
});
