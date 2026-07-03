<?php

declare(strict_types=1);

use App\Enums\TransportDirection;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;

function createTransportOffer(TransportDirection $direction = TransportDirection::Both, int $seats = 4): TransportOffer
{
    $sportList = SportList::query()->create(['short_name' => 'OB']);
    $sportEvent = SportEvent::factory()->create([
        'sport_id' => $sportList->id,
        'discipline_id' => null,
        'use_oris_for_entries' => false,
    ]);
    $driver = User::factory()->create();
    $vehicle = Vehicle::factory()->ownedBy($driver)->create();

    return TransportOffer::factory()->create([
        'sport_event_id' => $sportEvent->id,
        'user_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'direction' => $direction,
        'seats_offered' => $seats,
    ]);
}

it('computes free seats from approved requests only', function (): void {
    $offer = createTransportOffer(seats: 4);
    $passenger = User::factory()->create();

    TransportRequest::factory()->approved()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
        'direction' => TransportDirection::Both,
        'seats' => 2,
    ]);
    TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
        'direction' => TransportDirection::Both,
        'seats' => 1,
    ]);
    TransportRequest::factory()->rejected()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
        'direction' => TransportDirection::Both,
        'seats' => 1,
    ]);

    $offer->refresh()->load('requests');

    expect($offer->freeSeats())->toBe(2)
        ->and($offer->freeSeatsFor(TransportDirection::There))->toBe(2)
        ->and($offer->freeSeatsFor(TransportDirection::Back))->toBe(2);
});

it('tracks free seats per direction', function (): void {
    $offer = createTransportOffer(seats: 3);
    $passenger = User::factory()->create();

    TransportRequest::factory()->approved()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
        'direction' => TransportDirection::There,
        'seats' => 2,
    ]);

    $offer->refresh()->load('requests');

    expect($offer->freeSeatsFor(TransportDirection::There))->toBe(1)
        ->and($offer->freeSeatsFor(TransportDirection::Back))->toBe(3)
        ->and($offer->freeSeats())->toBe(1);
});

it('never reports negative free seats', function (): void {
    $offer = createTransportOffer(seats: 1);
    $passenger = User::factory()->create();

    TransportRequest::factory()->approved()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
        'direction' => TransportDirection::Both,
        'seats' => 2,
    ]);

    $offer->refresh()->load('requests');

    expect($offer->freeSeats())->toBe(0);
});

it('checks direction overlaps', function (): void {
    expect(TransportDirection::There->overlaps(TransportDirection::There))->toBeTrue()
        ->and(TransportDirection::There->overlaps(TransportDirection::Back))->toBeFalse()
        ->and(TransportDirection::There->overlaps(TransportDirection::Both))->toBeTrue()
        ->and(TransportDirection::Back->overlaps(TransportDirection::Both))->toBeTrue()
        ->and(TransportDirection::Both->overlaps(TransportDirection::Both))->toBeTrue();
});

it('allows only owner to manage the offer', function (): void {
    $offer = createTransportOffer();
    $owner = $offer->user;
    $otherUser = User::factory()->create();

    expect($owner?->can('update', $offer))->toBeTrue()
        ->and($owner?->can('delete', $offer))->toBeTrue()
        ->and($otherUser->can('update', $offer))->toBeFalse()
        ->and($otherUser->can('delete', $offer))->toBeFalse();
});
