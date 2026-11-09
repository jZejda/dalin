<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Models\SportEvent;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

dataset('admin export routes', [
    'iof' => 'admin.export.event-entry-iof',
    'csos' => 'admin.export.event-entry-csos',
]);

beforeEach(function (): void {
    foreach (AppRoles::cases() as $role) {
        Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
    }

    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $this->event = SportEvent::factory()->create(['name' => 'Oblastní žebříček']);
});

it('redirects guests to the panel login', function (string $route): void {
    $this->get(route($route, ['eventId' => $this->event->id]))
        ->assertRedirect(route('filament.admin.auth.login'));
})->with('admin export routes');

it('forbids users without an export role', function (string $route): void {
    $user = User::factory()->create();
    $user->assignRole(AppRoles::Member->value);

    $this->actingAs($user)
        ->get(route($route, ['eventId' => $this->event->id]))
        ->assertForbidden();
})->with('admin export routes');

it('lets the export roles download the entry list', function (string $route, AppRoles $role): void {
    $user = User::factory()->create();
    $user->assignRole($role->value);

    $this->actingAs($user)
        ->get(route($route, ['eventId' => $this->event->id]))
        ->assertOk()
        ->assertHeader('Content-Disposition');
})->with('admin export routes')->with([
    'super_admin' => AppRoles::SuperAdmin,
    'event_master' => AppRoles::EventMaster,
    'event_organizer' => AppRoles::EventOrganizer,
]);

it('no longer registers the dead xlsx export route', function (): void {
    $this->get('/admin/export/event-entry/' . $this->event->id)->assertNotFound();
});
