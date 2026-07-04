<?php

declare(strict_types=1);

use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserRaceProfile;
use App\Shared\Helpers\AppHelper;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function makeRoleIfMissing(string $roleName): void
{
    Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
}

/**
 * Shield jinak vrací 403 — pro renderovací testy povolíme všechny gaty,
 * viditelnost štítku se řídí rolí přes hasRole(), ne přes gate.
 */
function bypassShieldAuthorization(): void
{
    \Illuminate\Support\Facades\Gate::before(fn (): ?bool => true);
}

beforeEach(function (): void {
    makeRoleIfMissing('member');
    makeRoleIfMissing('event_master');
    makeRoleIfMissing('event_organizer');
    makeRoleIfMissing('super_admin');

    $this->pastDeadlineEvent = SportEvent::factory()->create([
        'use_oris_for_entries' => false,
        'oris_id' => null,
        'cancelled' => false,
        'discipline_id' => null,
        'stages' => null,
        'sport_id' => 1,
        'entry_date_1' => now()->subDays(3),
        'entry_date_2' => null,
        'entry_date_3' => null,
    ]);

    $this->pastDeadlineOrisEvent = SportEvent::factory()->create([
        'use_oris_for_entries' => true,
        'oris_id' => 8888,
        'cancelled' => false,
        'discipline_id' => null,
        'stages' => null,
        'sport_id' => 1,
        'entry_date_1' => now()->subDays(3),
        'entry_date_2' => null,
        'entry_date_3' => null,
    ]);
});

function makeUserWithProfileAndRole(string $role): User
{
    $user = User::factory()->create(['active' => true]);
    $user->assignRole($role);

    UserRaceProfile::query()->create([
        'user_id' => $user->id,
        'first_name' => 'Deadline',
        'last_name' => 'Tester',
        'reg_number' => 'DDT'.fake()->unique()->numberBetween(1000, 9999),
        'gender' => 'M',
        'active' => true,
    ]);

    return $user;
}

describe('AppHelper::allowModifyUserEntryAfterDeadline', function (): void {
    test('allows EventMaster on non-ORIS event', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('event_master'));

        expect(AppHelper::allowModifyUserEntryAfterDeadline($this->pastDeadlineEvent))->toBeTrue();
    });

    test('allows EventOrganizer on non-ORIS event', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('event_organizer'));

        expect(AppHelper::allowModifyUserEntryAfterDeadline($this->pastDeadlineEvent))->toBeTrue();
    });

    test('allows SuperAdmin on non-ORIS event', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('super_admin'));

        expect(AppHelper::allowModifyUserEntryAfterDeadline($this->pastDeadlineEvent))->toBeTrue();
    });

    test('denies plain Member on non-ORIS event', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('member'));

        expect(AppHelper::allowModifyUserEntryAfterDeadline($this->pastDeadlineEvent))->toBeFalse();
    });

    test('denies EventMaster on event with ORIS entries', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('event_master'));

        expect(AppHelper::allowModifyUserEntryAfterDeadline($this->pastDeadlineOrisEvent))->toBeFalse();
    });

    test('allows EventMaster on ORIS event that does not use ORIS entries', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('event_master'));

        $event = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => 7777,
            'cancelled' => false,
            'discipline_id' => null,
            'stages' => null,
            'sport_id' => 1,
            'entry_date_1' => now()->subDays(3),
            'entry_date_2' => null,
            'entry_date_3' => null,
        ]);

        expect(AppHelper::allowModifyUserEntryAfterDeadline($event))->toBeTrue();
    });

    test('denies guest', function (): void {
        expect(AppHelper::allowModifyUserEntryAfterDeadline($this->pastDeadlineEvent))->toBeFalse();
    });
});

describe('UserRaceProfiles::getUserRaceProfiles after deadline', function (): void {
    test('returns profiles for EventMaster on non-ORIS event', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('event_master'));

        $profiles = (new UserRaceProfiles())->getUserRaceProfiles($this->pastDeadlineEvent);

        expect($profiles)->not->toBeEmpty();
    });

    test('returns empty collection for plain Member on non-ORIS event', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('member'));

        $profiles = (new UserRaceProfiles())->getUserRaceProfiles($this->pastDeadlineEvent);

        expect($profiles)->toBeEmpty();
    });

    test('returns empty collection for EventMaster on event with ORIS entries', function (): void {
        $this->actingAs(makeUserWithProfileAndRole('event_master'));

        $profiles = (new UserRaceProfiles())->getUserRaceProfiles($this->pastDeadlineOrisEvent);

        expect($profiles)->toBeEmpty();
    });
});

describe('entry page badge after deadline', function (): void {
    test('shows admin-mode badge for EventMaster on non-ORIS event after deadline', function (): void {
        bypassShieldAuthorization();
        $this->actingAs(makeUserWithProfileAndRole('event_master'));

        $url = \App\Filament\Resources\SportEvents\SportEventResource::getUrl('entry', [
            'record' => $this->pastDeadlineEvent->id,
        ]);

        $this->get($url)->assertOk()->assertSee('režim správce');
    });

    test('does not show badge for plain Member', function (): void {
        bypassShieldAuthorization();
        $this->actingAs(makeUserWithProfileAndRole('member'));

        $url = \App\Filament\Resources\SportEvents\SportEventResource::getUrl('entry', [
            'record' => $this->pastDeadlineEvent->id,
        ]);

        $this->get($url)->assertOk()->assertDontSee('režim správce');
    });

    test('does not show badge for EventMaster before deadline', function (): void {
        bypassShieldAuthorization();
        $this->actingAs(makeUserWithProfileAndRole('event_master'));

        $event = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => null,
            'stages' => null,
            'sport_id' => 1,
            'entry_date_1' => now()->addDays(3),
            'entry_date_2' => null,
            'entry_date_3' => null,
        ]);

        $url = \App\Filament\Resources\SportEvents\SportEventResource::getUrl('entry', [
            'record' => $event->id,
        ]);

        $this->get($url)->assertOk()->assertDontSee('režim správce');
    });
});
