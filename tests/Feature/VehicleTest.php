<?php

declare(strict_types=1);

use App\Enums\VehicleType;
use App\Filament\Clusters\Other\Pages\MyVehicleList;
use App\Models\AppSetting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

it('creates club vehicle without owner', function (): void {
    $vehicle = Vehicle::factory()->create();

    expect($vehicle->user_id)->toBeNull()
        ->and($vehicle->isClubVehicle())->toBeTrue()
        ->and($vehicle->active)->toBeTrue();
});

it('scopes club, owned and active vehicles', function (): void {
    $user = User::factory()->create();

    $clubVehicle = Vehicle::factory()->create();
    $ownVehicle = Vehicle::factory()->ownedBy($user)->create();
    $inactiveOwnVehicle = Vehicle::factory()->ownedBy($user)->inactive()->create();

    expect(Vehicle::query()->club()->pluck('id'))->toContain($clubVehicle->id)
        ->not->toContain($ownVehicle->id)
        ->and(Vehicle::query()->ownedBy($user->id)->pluck('id'))
        ->toContain($ownVehicle->id, $inactiveOwnVehicle->id)
        ->not->toContain($clubVehicle->id)
        ->and(Vehicle::query()->ownedBy($user->id)->active()->pluck('id'))
        ->toContain($ownVehicle->id)
        ->not->toContain($inactiveOwnVehicle->id);
});

it('soft deletes vehicles', function (): void {
    $vehicle = Vehicle::factory()->create();
    $vehicle->delete();

    expect(Vehicle::query()->find($vehicle->id))->toBeNull()
        ->and(Vehicle::withTrashed()->find($vehicle->id))->not->toBeNull();
});

it('allows owner to manage own vehicle only', function (): void {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $vehicle = Vehicle::factory()->ownedBy($owner)->create();

    expect($owner->can('update', $vehicle))->toBeTrue()
        ->and($owner->can('delete', $vehicle))->toBeTrue()
        ->and($owner->can('view', $vehicle))->toBeTrue()
        ->and($otherUser->can('update', $vehicle))->toBeFalse()
        ->and($otherUser->can('delete', $vehicle))->toBeFalse();
});

it('denies club vehicle management to member without permission', function (): void {
    $member = User::factory()->create();
    $clubVehicle = Vehicle::factory()->create();

    expect($member->can('update', $clubVehicle))->toBeFalse()
        ->and($member->can('delete', $clubVehicle))->toBeFalse()
        ->and($member->can('viewAny', Vehicle::class))->toBeFalse();
});

it('casts vehicle type to enum and defaults to passenger car', function (): void {
    $vehicle = Vehicle::factory()->create();

    expect($vehicle->refresh()->type)->toBe(VehicleType::PassengerCar);

    $bus = Vehicle::factory()->ofType(VehicleType::Bus)->create();

    expect($bus->refresh()->type)->toBe(VehicleType::Bus);
});

it('keeps only one default vehicle per owner', function (): void {
    $user = User::factory()->create();

    $first = Vehicle::factory()->ownedBy($user)->default()->create();
    $second = Vehicle::factory()->ownedBy($user)->default()->create();

    expect($first->refresh()->is_default)->toBeFalse()
        ->and($second->refresh()->is_default)->toBeTrue()
        ->and(Vehicle::query()->ownedBy($user->id)->default()->pluck('id')->all())
        ->toBe([$second->id]);
});

it('does not let a new default club vehicle affect a members default vehicle', function (): void {
    $user = User::factory()->create();
    $ownDefault = Vehicle::factory()->ownedBy($user)->default()->create();
    $clubDefault = Vehicle::factory()->default()->create();

    expect($ownDefault->refresh()->is_default)->toBeTrue()
        ->and($clubDefault->refresh()->is_default)->toBeTrue();
});

it('shows my vehicles page only when transport module is enabled', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, false);
    expect(MyVehicleList::canAccess())->toBeFalse();

    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
    expect(MyVehicleList::canAccess())->toBeTrue();
});
