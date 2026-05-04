<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\Pages\EntrySportEvent;
use App\Http\Components\Oris\Response\CreateEntry;
use App\Http\Components\Oris\Response\Entity\EntryData\Data;
use App\Http\Components\Oris\Response\Entity\EntryData\Entry as OrisEntry;
use App\Models\RelayTeam;
use App\Models\RelayTeamMember;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Charakterizační testy pro EntrySportEvent privátní metody.
 *
 * Tyto testy zafixovávají STÁVAJÍCÍ chování před refaktorem.
 * Po refaktoru budou tyto testy přepsané proti extrahovaným službám.
 *
 * Testují přes Reflection, protože metody jsou private – po jejich
 * extrakci do samostatných tříd bude testování přímé.
 */
function invokeOnEntryPage(string $method, array $args = [], ?SportEvent $record = null): mixed
{
    $page = new EntrySportEvent;

    if ($record !== null) {
        $page->record = $record;
    }

    $reflection = new ReflectionMethod($page, $method);
    $reflection->setAccessible(true);

    return $reflection->invoke($page, ...$args);
}

function ensureRoleExists(string $roleName): void
{
    Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
}

function makeRelayDiscipline(string $shortName = 'ST', string $longName = 'Štafety'): SportDiscipline
{
    $discipline = new SportDiscipline;
    $discipline->short_name = $shortName;
    $discipline->long_name = $longName;
    $discipline->saveOrFail();

    return $discipline;
}

beforeEach(function (): void {
    $this->user = User::factory()->create(['active' => true]);

    $this->raceProfile = UserRaceProfile::query()->create([
        'user_id' => $this->user->id,
        'first_name' => 'Test',
        'last_name' => 'Runner',
        'reg_number' => 'TST1001',
        'gender' => 'M',
        'active' => true,
    ]);

    $this->classDefinition = SportClassDefinition::query()->create([
        'sport_id' => 1,
        'age_from' => 18,
        'age_to' => 40,
        'gender' => 'M',
        'name' => 'H21',
    ]);

    $this->event = SportEvent::factory()->create([
        'use_oris_for_entries' => false,
        'oris_id' => null,
        'cancelled' => false,
        'discipline_id' => null,
        'stages' => null,
        'sport_id' => 1,
    ]);

    $this->sportClass = SportClass::query()->create([
        'sport_event_id' => $this->event->id,
        'class_definition_id' => $this->classDefinition->id,
        'name' => 'H21',
    ]);

    $this->validEntryData = [
        'note' => 'Pozn pořadateli',
        'club_note' => 'Klubová poznámka',
        'requested_start' => '(E0;brzy;)',
        'si' => 12345,
        'rent_si' => 1,
    ];
});

// =====================================================================
// 1.2  storeUserEntry – non-ORIS
// =====================================================================
describe('storeUserEntry (non-ORIS)', function (): void {
    test('creates UserEntry with all expected fields', function (): void {
        $entry = invokeOnEntryPage('storeUserEntry', [
            false,
            $this->event,
            $this->raceProfile,
            $this->sportClass,
            $this->validEntryData,
            null,
        ]);

        expect($entry)->toBeInstanceOf(UserEntry::class)
            ->and($entry->exists)->toBeTrue()
            ->and($entry->sport_event_id)->toBe($this->event->id)
            ->and($entry->user_race_profile_id)->toBe($this->raceProfile->id)
            ->and($entry->class_definition_id)->toBe($this->classDefinition->id)
            ->and($entry->class_name)->toBe('H21')
            ->and($entry->note)->toBe('Pozn pořadateli')
            ->and($entry->club_note)->toBe('Klubová poznámka')
            ->and($entry->requested_start)->toBe('(E0;brzy;)')
            ->and($entry->si)->toBe(12345)
            ->and($entry->rent_si)->toBeTrue()
            ->and($entry->entry_status)->toBe(EntryStatus::Create)
            ->and($entry->oris_entry_id)->toBeNull()
            ->and($entry->entry_created)->not->toBeNull();
    });

    test('persists entry_stages when provided', function (): void {
        $data = $this->validEntryData + ['entry_stages' => ['stage1', 'stage2']];

        $entry = invokeOnEntryPage('storeUserEntry', [
            false, $this->event, $this->raceProfile, $this->sportClass, $data, null,
        ]);

        expect($entry->entry_stages)->toBe(['stage1', 'stage2']);
    });

    test('returns null when userRaceProfile is missing', function (): void {
        $entry = invokeOnEntryPage('storeUserEntry', [
            false, $this->event, null, $this->sportClass, $this->validEntryData, null,
        ]);

        expect($entry)->toBeNull()
            ->and(UserEntry::query()->where('sport_event_id', $this->event->id)->count())->toBe(0);
    });

    test('returns null when sportClass is missing', function (): void {
        $entry = invokeOnEntryPage('storeUserEntry', [
            false, $this->event, $this->raceProfile, null, $this->validEntryData, null,
        ]);

        expect($entry)->toBeNull()
            ->and(UserEntry::query()->where('sport_event_id', $this->event->id)->count())->toBe(0);
    });

    test('defaults rent_si to 0 when missing from data', function (): void {
        $data = $this->validEntryData;
        unset($data['rent_si']);

        $entry = invokeOnEntryPage('storeUserEntry', [
            false, $this->event, $this->raceProfile, $this->sportClass, $data, null,
        ]);

        expect($entry->rent_si)->toBeFalse();
    });
});

// =====================================================================
// 1.3  storeUserEntry – ORIS
// =====================================================================
describe('storeUserEntry (ORIS)', function (): void {
    test('sets oris_entry_id from ORIS response', function (): void {
        $orisResponse = new CreateEntry(
            Method: 'createEntry',
            Format: 'json',
            Status: 'OK',
            ExportCreated: '2026-05-04 00:00:00',
            Data: new Data(Entry: new OrisEntry(ID: 9876543)),
        );

        $entry = invokeOnEntryPage('storeUserEntry', [
            true,
            $this->event,
            $this->raceProfile,
            $this->sportClass,
            $this->validEntryData,
            $orisResponse,
        ]);

        expect($entry)->toBeInstanceOf(UserEntry::class)
            ->and($entry->oris_entry_id)->toBe(9876543);
    });

    test('handles ORIS response with null Data gracefully', function (): void {
        $orisResponse = new CreateEntry(
            Method: 'createEntry',
            Format: 'json',
            Status: 'OK',
            ExportCreated: '2026-05-04 00:00:00',
            Data: null,
        );

        $entry = invokeOnEntryPage('storeUserEntry', [
            true,
            $this->event,
            $this->raceProfile,
            $this->sportClass,
            $this->validEntryData,
            $orisResponse,
        ]);

        expect($entry)->toBeInstanceOf(UserEntry::class)
            ->and($entry->oris_entry_id)->toBeNull();
    });
});

// =====================================================================
// 1.4  storeRelayUserEntry – rezervace slotu
// =====================================================================
describe('storeRelayUserEntry', function (): void {
    beforeEach(function (): void {
        $this->relayDiscipline = makeRelayDiscipline('ST', 'Štafety');

        $this->relayEvent = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => $this->relayDiscipline->id,
            'sport_id' => 1,
        ]);

        $this->relaySportClass = SportClass::query()->create([
            'sport_event_id' => $this->relayEvent->id,
            'class_definition_id' => $this->classDefinition->id,
            'name' => 'D21',
        ]);

        // SportEvent boot creates "Štafeta 1" team automatically – attach class.
        $this->relayTeam = RelayTeam::query()
            ->where('sport_event_id', $this->relayEvent->id)
            ->firstOrFail();
        $this->relayTeam->sport_class_id = $this->relaySportClass->id;
        $this->relayTeam->saveOrFail();

        $this->freeSlot = $this->relayTeam->members()->where('slot', 1)->firstOrFail();
    });

    test('reserves a free slot and binds UserEntry + UserRaceProfile', function (): void {
        $result = invokeOnEntryPage('storeRelayUserEntry', [
            $this->relayEvent,
            $this->raceProfile,
            ['relayTeamMemberId' => $this->freeSlot->id] + $this->validEntryData,
        ]);

        $this->freeSlot->refresh();
        $entry = UserEntry::query()->latest('id')->first();

        expect($result)->toBeTrue()
            ->and($this->freeSlot->user_race_profile_id)->toBe($this->raceProfile->id)
            ->and($this->freeSlot->user_entry_id)->toBe($entry->id)
            ->and($entry->class_name)->toBe('D21');
    });

    test('returns false when slot is already taken', function (): void {
        $existingEntry = UserEntry::query()->create([
            'sport_event_id' => $this->relayEvent->id,
            'class_definition_id' => $this->classDefinition->id,
            'user_race_profile_id' => $this->raceProfile->id,
            'class_name' => 'D21',
            'entry_status' => EntryStatus::Create->value,
            'rent_si' => false,
            'entry_created' => now(),
        ]);
        $this->freeSlot->user_entry_id = $existingEntry->id;
        $this->freeSlot->saveOrFail();

        $result = invokeOnEntryPage('storeRelayUserEntry', [
            $this->relayEvent,
            $this->raceProfile,
            ['relayTeamMemberId' => $this->freeSlot->id] + $this->validEntryData,
        ]);

        expect($result)->toBeFalse();
    });

    test('returns false when slot belongs to a different event', function (): void {
        $otherRelayEvent = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => $this->relayDiscipline->id,
            'sport_id' => 1,
        ]);
        $otherTeam = RelayTeam::query()->where('sport_event_id', $otherRelayEvent->id)->firstOrFail();
        $otherSlot = $otherTeam->members()->first();

        $result = invokeOnEntryPage('storeRelayUserEntry', [
            $this->relayEvent,
            $this->raceProfile,
            ['relayTeamMemberId' => $otherSlot->id] + $this->validEntryData,
        ]);

        expect($result)->toBeFalse();
    });

    test('returns false when relayTeamMemberId is missing', function (): void {
        $result = invokeOnEntryPage('storeRelayUserEntry', [
            $this->relayEvent,
            $this->raceProfile,
            $this->validEntryData,
        ]);

        expect($result)->toBeFalse();
    });

    test('returns false when userRaceProfile is null', function (): void {
        $result = invokeOnEntryPage('storeRelayUserEntry', [
            $this->relayEvent,
            null,
            ['relayTeamMemberId' => $this->freeSlot->id] + $this->validEntryData,
        ]);

        expect($result)->toBeFalse();
    });
});

// =====================================================================
// 1.5  releaseRelaySlot
// =====================================================================
describe('releaseRelaySlot', function (): void {
    test('clears slot binding when entry has relayTeamMember', function (): void {
        $relayDiscipline = makeRelayDiscipline('SS', 'Sprintové štafety');
        $relayEvent = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => $relayDiscipline->id,
            'sport_id' => 1,
        ]);
        $team = RelayTeam::query()->where('sport_event_id', $relayEvent->id)->firstOrFail();
        $slot = $team->members()->first();

        $entry = UserEntry::query()->create([
            'sport_event_id' => $relayEvent->id,
            'class_definition_id' => $this->classDefinition->id,
            'user_race_profile_id' => $this->raceProfile->id,
            'class_name' => 'H21',
            'entry_status' => EntryStatus::Create->value,
            'rent_si' => false,
            'entry_created' => now(),
        ]);
        $slot->user_race_profile_id = $this->raceProfile->id;
        $slot->user_entry_id = $entry->id;
        $slot->saveOrFail();

        invokeOnEntryPage('releaseRelaySlot', [$entry->fresh()]);

        $slot->refresh();
        expect($slot->user_entry_id)->toBeNull()
            ->and($slot->user_race_profile_id)->toBeNull();
    });

    test('is no-op when entry has no relay slot', function (): void {
        $entry = UserEntry::query()->create([
            'sport_event_id' => $this->event->id,
            'class_definition_id' => $this->classDefinition->id,
            'user_race_profile_id' => $this->raceProfile->id,
            'class_name' => 'H21',
            'entry_status' => EntryStatus::Create->value,
            'rent_si' => false,
            'entry_created' => now(),
        ]);

        // should not throw
        invokeOnEntryPage('releaseRelaySlot', [$entry]);

        expect(true)->toBeTrue();
    });
});

// =====================================================================
// 1.6  getAvailableRelayMemberSlots
// =====================================================================
describe('getAvailableRelayMemberSlots', function (): void {
    test('returns only free slots for given event ordered by team name + slot', function (): void {
        $relayDiscipline = makeRelayDiscipline('DR', 'Družstva');
        $event = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => $relayDiscipline->id,
            'sport_id' => 1,
        ]);

        $teamA = RelayTeam::query()->where('sport_event_id', $event->id)->firstOrFail();
        $teamA->name = 'Alfa';
        $teamA->saveOrFail();

        $teamB = RelayTeam::query()->create([
            'sport_event_id' => $event->id,
            'name' => 'Beta',
            'relay_type' => 'DR',
            'slots_count' => 2,
        ]);

        // Reserve teamA slot 2 (will appear taken)
        $takenEntry = UserEntry::query()->create([
            'sport_event_id' => $event->id,
            'class_definition_id' => $this->classDefinition->id,
            'user_race_profile_id' => $this->raceProfile->id,
            'class_name' => 'H21',
            'entry_status' => EntryStatus::Create->value,
            'rent_si' => false,
            'entry_created' => now(),
        ]);
        $taken = $teamA->members()->where('slot', 2)->firstOrFail();
        $taken->user_entry_id = $takenEntry->id;
        $taken->saveOrFail();

        $slots = invokeOnEntryPage('getAvailableRelayMemberSlots', [$event]);

        // teamA: slots 1, 3 free; teamB: slots 1, 2 free → 4 total
        expect($slots)->toHaveCount(4);

        $labels = $slots->values()->all();
        // First two should be Alfa, then Beta (sorted by team name)
        expect($labels[0])->toContain('Alfa')->toContain('slot 1')
            ->and($labels[1])->toContain('Alfa')->toContain('slot 3')
            ->and($labels[2])->toContain('Beta')->toContain('slot 1')
            ->and($labels[3])->toContain('Beta')->toContain('slot 2');
    });

    test('does not return slots from other events', function (): void {
        $relayDiscipline = makeRelayDiscipline('ST', 'Štafety A');
        $eventA = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => $relayDiscipline->id,
            'sport_id' => 1,
        ]);
        $eventB = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => $relayDiscipline->id,
            'sport_id' => 1,
        ]);

        $slotsA = invokeOnEntryPage('getAvailableRelayMemberSlots', [$eventA]);
        $slotsB = invokeOnEntryPage('getAvailableRelayMemberSlots', [$eventB]);

        expect($slotsA)->toHaveCount(3)
            ->and($slotsB)->toHaveCount(3)
            ->and($slotsA->keys()->intersect($slotsB->keys())->all())->toBe([]);
    });
});

// =====================================================================
// 1.8  Page render smoke test (auth + Filament boot)
// =====================================================================
describe('page render', function (): void {
    test('renders for SuperAdmin (non-ORIS event)', function (): void {
        actingAsSuperAdmin();

        $url = \App\Filament\Resources\SportEvents\SportEventResource::getUrl('entry', [
            'record' => $this->event->id,
        ]);

        $this->get($url)->assertOk();
    });

    test('renders for SuperAdmin (relay event)', function (): void {
        actingAsSuperAdmin();

        $relay = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => makeRelayDiscipline('ST', 'Štafety SA')->id,
            'sport_id' => 1,
        ]);

        $url = \App\Filament\Resources\SportEvents\SportEventResource::getUrl('entry', [
            'record' => $relay->id,
        ]);

        $this->get($url)->assertOk();
    });
});

// =====================================================================
// 1.7  hideDeleteAction matrix
// =====================================================================
describe('hideDeleteAction', function (): void {
    beforeEach(function (): void {
        ensureRoleExists('member');
        ensureRoleExists('event_master');
        ensureRoleExists('event_organizer');

        $this->ownerEntry = UserEntry::query()->create([
            'sport_event_id' => $this->event->id,
            'class_definition_id' => $this->classDefinition->id,
            'user_race_profile_id' => $this->raceProfile->id,
            'class_name' => 'H21',
            'entry_status' => EntryStatus::Create->value,
            'rent_si' => false,
            'entry_created' => now(),
        ]);
    });

    test('returns false (visible) for EventMaster regardless of ownership', function (): void {
        $eventMaster = User::factory()->create(['active' => true]);
        $eventMaster->assignRole('event_master');
        $this->actingAs($eventMaster);

        $result = invokeOnEntryPage('hideDeleteAction', [$this->ownerEntry], $this->event);

        expect($result)->toBeFalse();
    });

    test('returns false (visible) for EventOrganizer', function (): void {
        $organizer = User::factory()->create(['active' => true]);
        $organizer->assignRole('event_organizer');
        $this->actingAs($organizer);

        $result = invokeOnEntryPage('hideDeleteAction', [$this->ownerEntry], $this->event);

        expect($result)->toBeFalse();
    });

    test('returns false (visible) for owner of UserRaceProfile', function (): void {
        $this->user->assignRole('member');
        $this->actingAs($this->user);

        $result = invokeOnEntryPage('hideDeleteAction', [$this->ownerEntry], $this->event);

        expect($result)->toBeFalse();
    });

    test('returns true (hidden) for unrelated user', function (): void {
        $otherUser = User::factory()->create(['active' => true]);
        $otherUser->assignRole('member');
        $this->actingAs($otherUser);

        $result = invokeOnEntryPage('hideDeleteAction', [$this->ownerEntry], $this->event);

        expect($result)->toBeTrue();
    });

    test('returns true (hidden) when entry is already cancelled (and viewer is not EventMaster)', function (): void {
        $this->user->assignRole('member');
        $this->actingAs($this->user);

        $this->ownerEntry->entry_status = EntryStatus::Cancel;
        $this->ownerEntry->saveOrFail();

        $result = invokeOnEntryPage('hideDeleteAction', [$this->ownerEntry->fresh()], $this->event);

        expect($result)->toBeTrue();
    });
});
