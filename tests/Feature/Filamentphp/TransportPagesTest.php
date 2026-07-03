<?php

declare(strict_types=1);

use App\Models\AppSetting;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

it('renders club vehicles resource for super admin', function (): void {
    actingAsSuperAdmin();

    Vehicle::factory()->create();

    $this->get('/admin/transport/vehicles')->assertOk();
    $this->get('/admin/transport/vehicles/create')->assertOk();
});

it('renders transport settings page for super admin', function (): void {
    actingAsSuperAdmin();

    $this->get('/admin/transport/transport-settings')->assertOk();
});

it('denies club vehicles resource to plain member', function (): void {
    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get('/admin/transport/vehicles')->assertForbidden();
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

function createTransportTestSportEvent(): SportEvent
{
    $sportList = SportList::query()->create(['short_name' => 'OB']);

    return SportEvent::factory()->create([
        'sport_id' => $sportList->id,
        'discipline_id' => null,
        'use_oris_for_entries' => false,
    ]);
}

it('denies my vehicles page when module is disabled', function (): void {
    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, false);

    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get('/admin/my-vehicle-list')->assertForbidden();
});
