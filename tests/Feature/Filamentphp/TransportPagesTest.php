<?php

declare(strict_types=1);

use App\Enums\SportEventTransportType;
use App\Models\AppSetting;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\TransportOffer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function (): void {
    Cache::flush();
});

it('renders club vehicles resource for super admin', function (): void {
    actingAsSuperAdmin();

    Vehicle::factory()->create();

    $this->get('/admin/config/vehicles')->assertOk();
    $this->get('/admin/config/vehicles/create')->assertOk();
});

it('renders transport settings page for super admin', function (): void {
    actingAsSuperAdmin();

    $this->get('/admin/config/settings')->assertOk();
});

it('denies club vehicles resource to plain member', function (): void {
    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get('/admin/config/vehicles')->assertForbidden();
});

it('renders my vehicles page for member when module is enabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);

    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get('/admin/my-vehicle-list')->assertOk();
});

it('shows transport type select on sport event form when module is enabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);

    actingAsSuperAdmin();
    $sportEvent = createTransportTestSportEvent();

    $this->get('/admin/sport-events/'.$sportEvent->id.'/edit')
        ->assertOk()
        ->assertSee(__('sport-event.transport_type'));
});

it('hides transport type select on sport event form when module is disabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, false);

    actingAsSuperAdmin();
    $sportEvent = createTransportTestSportEvent();

    $this->get('/admin/sport-events/'.$sportEvent->id.'/edit')
        ->assertOk()
        ->assertDontSee(__('sport-event.transport_type'));
});

function createTransportTestSportEvent(SportEventTransportType $transportType = SportEventTransportType::SelfOnly): SportEvent
{
    $sportList = SportList::query()->create(['short_name' => 'OB']);

    return SportEvent::factory()->create([
        'sport_id' => $sportList->id,
        'discipline_id' => null,
        'use_oris_for_entries' => false,
        'transport_type' => $transportType,
    ]);
}

function actingAsMember(): User
{
    $role = Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
    $permission = Permission::firstOrCreate(['name' => 'ViewAny:SportEvent', 'guard_name' => 'web']);
    $role->givePermissionTo($permission);
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $member = User::factory()->create(['active' => true]);
    $member->assignRole('member');
    test()->actingAs($member);

    return $member;
}

it('renders transport page for member when module is enabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
    actingAsMember();

    $sportEvent = createTransportTestSportEvent();

    $this->get('/admin/sport-events/'.$sportEvent->id.'/transport')
        ->assertOk()
        ->assertSee(__('transport.offer_transport'));
});

it('returns 404 on transport page when module is disabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, false);
    actingAsMember();

    $sportEvent = createTransportTestSportEvent();

    $this->get('/admin/sport-events/'.$sportEvent->id.'/transport')
        ->assertNotFound();
});

it('returns 404 on transport page when event has no transport', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
    actingAsMember();

    $sportEvent = createTransportTestSportEvent(SportEventTransportType::None);

    $this->get('/admin/sport-events/'.$sportEvent->id.'/transport')
        ->assertNotFound();
});

it('shows transport link on entry page when module is enabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
    actingAsMember();

    $sportEvent = createTransportTestSportEvent();

    $this->get('/admin/sport-events/'.$sportEvent->id.'/entry')
        ->assertOk()
        ->assertSee('/admin/sport-events/'.$sportEvent->id.'/transport');
});

it('denies my vehicles page when module is disabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, false);

    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get('/admin/my-vehicle-list')->assertForbidden();
});

it('shows existing offer in transport page table', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
    $member = actingAsMember();

    $sportEvent = createTransportTestSportEvent();
    $vehicle = Vehicle::factory()->ownedBy($member)->create();
    TransportOffer::factory()->create([
        'sport_event_id' => $sportEvent->id,
        'user_id' => $member->id,
        'vehicle_id' => $vehicle->id,
        'departure_place' => 'Testovací nástupiště',
    ]);

    $this->get('/admin/sport-events/'.$sportEvent->id.'/transport')
        ->assertOk()
        ->assertSee('Testovací nástupiště')
        ->assertSee($vehicle->name);
});
