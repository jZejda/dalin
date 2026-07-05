<?php

declare(strict_types=1);

use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Creates an active member with an API key for the sport-event endpoints.
 */
function sportEventApiUser(): User
{
    $user = User::factory()->create(['active' => true]);

    Role::findOrCreate(User::ROLE_MEMBER);
    $user->assignRole(User::ROLE_MEMBER);
    $user->setApiKey('test-api-key-se-' . $user->id);

    return $user;
}

function sportEventWithClass(array $eventAttributes = []): array
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

    return [$event, $class, $definition];
}

beforeEach(function (): void {
    $this->user = sportEventApiUser();
    $this->asApiUser = fn (User $user) => $this->withHeader('x-apikey', (string) $user->api_key_hash);
});

// ---------------------------------------------------------------------------
// GET /api/v1/sport-event (member access)
// ---------------------------------------------------------------------------

test('sport-event list: unauthenticated request is rejected with 401', function (): void {
    $this->getJson('/api/v1/sport-event')
        ->assertUnauthorized();
});

test('sport-event list: member role can access the list', function (): void {
    ($this->asApiUser)($this->user)
        ->getJson('/api/v1/sport-event')
        ->assertOk();
});

test('sport-event list: items contain oris and cancelled flags', function (): void {
    sportEventWithClass();

    $item = ($this->asApiUser)($this->user)
        ->getJson('/api/v1/sport-event?from=2000-01-01')
        ->assertOk()
        ->json('data.0');

    expect($item)->toHaveKeys([
        'id',
        'name',
        'date',
        'entry_date',
        'event_type',
        'oris_id',
        'use_oris_for_entries',
        'cancelled',
        'categories',
    ]);
});

// ---------------------------------------------------------------------------
// GET /api/v1/sport-event/{sportEvent}
// ---------------------------------------------------------------------------

test('sport-event detail: unauthenticated request is rejected with 401', function (): void {
    $this->getJson('/api/v1/sport-event/1')
        ->assertUnauthorized();
});

test('sport-event detail: returns 404 for unknown event', function (): void {
    ($this->asApiUser)($this->user)
        ->getJson('/api/v1/sport-event/999999999')
        ->assertNotFound();
});

test('sport-event detail: contains all related objects', function (): void {
    [$event, $class, $definition] = sportEventWithClass();

    $data = ($this->asApiUser)($this->user)
        ->getJson('/api/v1/sport-event/' . $event->id)
        ->assertOk()
        ->json('data');

    expect($data)->toHaveKeys([
        'id',
        'name',
        'oris_id',
        'use_oris_for_entries',
        'date',
        'event_type',
        'discipline',
        'level',
        'is_relay',
        'cancelled',
        'entry_dates',
        'entry_deadline_passed',
        'gps',
        'stages',
        'stage_options',
        'classes',
        'services',
        'links',
        'relay_teams',
    ])
        ->and($data['id'])->toBe($event->id)
        ->and($data['is_relay'])->toBeFalse()
        ->and($data['classes'])->toHaveCount(1)
        ->and($data['classes'][0]['id'])->toBe($class->id)
        ->and($data['classes'][0]['class_definition']['id'])->toBe($definition->id);
});

test('sport-event detail: entry_deadline_passed is true after the deadline', function (): void {
    [$event] = sportEventWithClass([
        'entry_date_1' => now()->subDays(2),
    ]);

    $data = ($this->asApiUser)($this->user)
        ->getJson('/api/v1/sport-event/' . $event->id)
        ->assertOk()
        ->json('data');

    expect($data['entry_deadline_passed'])->toBeTrue()
        ->and($data['entry_dates']['last_entry_date'])->not->toBeNull();
});
