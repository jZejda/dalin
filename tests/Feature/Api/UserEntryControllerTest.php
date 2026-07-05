<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Spatie\Permission\Models\Role;

/**
 * Creates an active member with an API key for the entry endpoints.
 */
function entryApiUser(): User
{
    $user = User::factory()->create(['active' => true]);

    Role::findOrCreate(User::ROLE_MEMBER);
    $user->assignRole(User::ROLE_MEMBER);
    $user->setApiKey('test-api-key-ue-' . $user->id);

    return $user;
}

function entryRaceProfile(User $user, array $attributes = []): UserRaceProfile
{
    return UserRaceProfile::create(array_merge([
        'user_id'    => $user->id,
        'first_name' => 'Jan',
        'last_name'  => 'Novák',
        'reg_number' => 'ABB' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
        'gender'     => 'M',
        'active'     => true,
    ], $attributes));
}

/**
 * Manual (non-ORIS, non-relay) event with one class, open for entries.
 *
 * @return array{0: SportEvent, 1: SportClass}
 */
function manualEntryEvent(array $eventAttributes = []): array
{
    $sportList = SportList::query()->firstOrCreate(['short_name' => 'OB']);

    $event = SportEvent::factory()->create(array_merge([
        'oris_id' => null,
        'use_oris_for_entries' => false,
        'discipline_id' => null,
        'cancelled' => false,
        'stages' => null,
        'date' => now()->addDays(20)->toDateString(),
        'entry_date_1' => now()->addDays(10),
        'entry_date_2' => null,
        'entry_date_3' => null,
    ], $eventAttributes));

    $definition = SportClassDefinition::query()->create([
        'sport_id' => $sportList->id,
        'age_from' => 21,
        'age_to' => 34,
        'gender' => 'M',
        'name' => 'H21',
    ]);

    $class = SportClass::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'name' => 'H21',
        'fee' => 150,
    ]);

    return [$event, $class];
}

beforeEach(function (): void {
    $this->user = entryApiUser();
    $this->raceProfile = entryRaceProfile($this->user);
    $this->asApiUser = fn (User $user) => $this->withHeader('x-apikey', (string) $user->api_key_hash);
});

// ---------------------------------------------------------------------------
// POST /api/v1/user/entry
// ---------------------------------------------------------------------------

test('entry store: unauthenticated request is rejected with 401', function (): void {
    $this->postJson('/api/v1/user/entry', [])
        ->assertUnauthorized();
});

test('entry store: missing required fields fails validation with 422', function (): void {
    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['sport_event_id', 'race_profile_id']);
});

test('entry store: race profile of another user is rejected with 403', function (): void {
    [$event, $class] = manualEntryEvent();

    $otherUser = User::factory()->create(['active' => true]);
    $foreignProfile = entryRaceProfile($otherUser, ['reg_number' => 'XYZ0001']);

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $foreignProfile->id,
            'class_id'        => $class->id,
        ])
        ->assertForbidden();
});

test('entry store: inactive race profile is rejected with 422', function (): void {
    [$event, $class] = manualEntryEvent();

    $inactiveProfile = entryRaceProfile($this->user, ['reg_number' => 'ABB9998', 'active' => false]);

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $inactiveProfile->id,
            'class_id'        => $class->id,
        ])
        ->assertUnprocessable();
});

test('entry store: cancelled event is rejected with 422', function (): void {
    [$event, $class] = manualEntryEvent(['cancelled' => true]);

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
            'class_id'        => $class->id,
        ])
        ->assertUnprocessable();
});

test('entry store: passed entry deadline is rejected with 422', function (): void {
    [$event, $class] = manualEntryEvent(['entry_date_1' => now()->subDays(1)]);

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
            'class_id'        => $class->id,
        ])
        ->assertUnprocessable();
});

test('entry store: missing class_id for non-relay event is rejected with 422', function (): void {
    [$event] = manualEntryEvent();

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
        ])
        ->assertUnprocessable();
});

test('entry store: class of a different event is rejected with 422', function (): void {
    [$event] = manualEntryEvent();
    [, $foreignClass] = manualEntryEvent();

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
            'class_id'        => $foreignClass->id,
        ])
        ->assertUnprocessable();
});

test('entry store: ORIS event with profile without oris_id is rejected with 422', function (): void {
    [$event, $class] = manualEntryEvent([
        'oris_id' => 8703,
        'use_oris_for_entries' => true,
    ]);
    $class->update(['oris_id' => 123456]);

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
            'class_id'        => $class->id,
        ])
        ->assertUnprocessable();
});

test('entry store: happy path creates the entry on a manual event', function (): void {
    [$event, $class] = manualEntryEvent();

    $response = ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
            'class_id'        => $class->id,
            'si'              => 8123456,
            'note'            => 'API note',
        ])
        ->assertCreated();

    expect($response->json('data.sport_event.id'))->toBe($event->id)
        ->and($response->json('data.race_profile.id'))->toBe($this->raceProfile->id)
        ->and($response->json('data.class_name'))->toBe('H21')
        ->and($response->json('data.entry_status'))->toBe(EntryStatus::Create->value);

    $entry = UserEntry::query()->where('sport_event_id', $event->id)->first();

    expect($entry)->not->toBeNull()
        ->and($entry->user_race_profile_id)->toBe($this->raceProfile->id)
        ->and($entry->si)->toBe(8123456)
        ->and($entry->note)->toBe('API note')
        ->and($entry->entry_status)->toBe(EntryStatus::Create);
});

test('entry store: duplicate entry for the same profile is rejected with 409', function (): void {
    [$event, $class] = manualEntryEvent();

    $payload = [
        'sport_event_id'  => $event->id,
        'race_profile_id' => $this->raceProfile->id,
        'class_id'        => $class->id,
    ];

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', $payload)
        ->assertCreated();

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', $payload)
        ->assertConflict();
});

test('entry store: multi-stage event requires entry_stages', function (): void {
    [$event, $class] = manualEntryEvent(['stages' => 2]);

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
            'class_id'        => $class->id,
        ])
        ->assertUnprocessable();

    ($this->asApiUser)($this->user)
        ->postJson('/api/v1/user/entry', [
            'sport_event_id'  => $event->id,
            'race_profile_id' => $this->raceProfile->id,
            'class_id'        => $class->id,
            'entry_stages'    => ['stage1', 'stage2'],
        ])
        ->assertCreated();
});

// ---------------------------------------------------------------------------
// DELETE /api/v1/user/entry/{userEntry}
// ---------------------------------------------------------------------------

function createEntryFor(UserRaceProfile $profile, SportEvent $event, EntryStatus $status = EntryStatus::Create): UserEntry
{
    $definition = SportClassDefinition::query()->firstOrCreate(
        ['name' => 'H21', 'sport_id' => SportList::query()->firstOrCreate(['short_name' => 'OB'])->id],
        ['age_from' => 21, 'age_to' => 34, 'gender' => 'M'],
    );

    return UserEntry::create([
        'sport_event_id'       => $event->id,
        'class_definition_id'  => $definition->id,
        'user_race_profile_id' => $profile->id,
        'class_name'           => 'H21',
        'entry_status'         => $status->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);
}

test('entry delete: unauthenticated request is rejected with 401', function (): void {
    $this->deleteJson('/api/v1/user/entry/1')
        ->assertUnauthorized();
});

test('entry delete: unknown entry returns 404', function (): void {
    ($this->asApiUser)($this->user)
        ->deleteJson('/api/v1/user/entry/999999999')
        ->assertNotFound();
});

test('entry delete: entry of another user returns 404', function (): void {
    [$event] = manualEntryEvent();

    $otherUser = User::factory()->create(['active' => true]);
    $foreignProfile = entryRaceProfile($otherUser, ['reg_number' => 'XYZ0002']);
    $foreignEntry = createEntryFor($foreignProfile, $event);

    ($this->asApiUser)($this->user)
        ->deleteJson('/api/v1/user/entry/' . $foreignEntry->id)
        ->assertNotFound();
});

test('entry delete: already cancelled entry is rejected with 422', function (): void {
    [$event] = manualEntryEvent();
    $entry = createEntryFor($this->raceProfile, $event, EntryStatus::Cancel);

    ($this->asApiUser)($this->user)
        ->deleteJson('/api/v1/user/entry/' . $entry->id)
        ->assertUnprocessable();
});

test('entry delete: passed entry deadline is rejected with 422', function (): void {
    [$event] = manualEntryEvent(['entry_date_1' => now()->subDays(1)]);
    $entry = createEntryFor($this->raceProfile, $event);

    ($this->asApiUser)($this->user)
        ->deleteJson('/api/v1/user/entry/' . $entry->id)
        ->assertUnprocessable();
});

test('entry delete: happy path cancels a local entry', function (): void {
    [$event] = manualEntryEvent();
    $entry = createEntryFor($this->raceProfile, $event);

    $response = ($this->asApiUser)($this->user)
        ->deleteJson('/api/v1/user/entry/' . $entry->id)
        ->assertOk();

    expect($response->json('data.id'))->toBe($entry->id)
        ->and($response->json('data.entry_status'))->toBe(EntryStatus::Cancel->value)
        ->and($response->json('data.was_oris_entry'))->toBeFalse()
        ->and($entry->fresh()->entry_status)->toBe(EntryStatus::Cancel);
});
