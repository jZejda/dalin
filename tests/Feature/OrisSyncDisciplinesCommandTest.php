<?php

declare(strict_types=1);

use App\Enums\RelayType;
use App\Models\RelayTeam;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

// High ids keep the test isolated from disciplines/events already present in the testing DB.
const TEST_MASS_START_ID = 9014;
const TEST_SPRINT_RELAY_ID = 9015;

beforeEach(function (): void {
    $disciplines = [
        TEST_MASS_START_ID => ['short_name' => 'MS', 'long_name' => 'Hromadný start', 'relays' => false],
        TEST_SPRINT_RELAY_ID => ['short_name' => 'SR', 'long_name' => 'Sprintové štafety', 'relays' => true],
    ];

    foreach ($disciplines as $id => $data) {
        $discipline = SportDiscipline::query()->find($id) ?? new SportDiscipline();
        $discipline->id = $id;
        $discipline->fill($data)->save();
    }
});

/**
 * @param array<int, int|null> $disciplineByOrisId ORIS event id => ORIS discipline id (null = ORIS error)
 */
function fakeOrisDisciplines(array $disciplineByOrisId): void
{
    Http::fake(function (Request $request) use ($disciplineByOrisId) {
        $disciplineId = $disciplineByOrisId[(int) $request['id']] ?? null;
        $envelope = ['Method' => 'getEvent', 'Format' => 'json', 'ExportCreated' => now()->toDateTimeString()];

        if ($disciplineId === null) {
            return Http::response([...$envelope, 'Status' => 'Error', 'Data' => []]);
        }

        return Http::response([
            ...$envelope,
            'Status' => 'OK',
            'Data' => ['ID' => (string) $request['id'], 'Discipline' => ['ID' => (string) $disciplineId]],
        ]);
    });
}

function runDisciplineSync(string ...$extra): Illuminate\Testing\PendingCommand
{
    return test()->artisan('oris:sync-disciplines', [
        '--discipline' => [TEST_MASS_START_ID, TEST_SPRINT_RELAY_ID],
        '--sleep' => 0,
        ...array_fill_keys($extra, true),
    ]);
}

test('fixes a stale discipline and creates the default relay team for an upcoming event', function (): void {
    $event = SportEvent::factory()->create([
        'oris_id' => 919285,
        'discipline_id' => TEST_MASS_START_ID,
        'date' => now()->addMonth(),
    ]);
    fakeOrisDisciplines([919285 => TEST_SPRINT_RELAY_ID]);

    runDisciplineSync()->assertSuccessful();

    $event->refresh();
    $team = RelayTeam::query()->where('sport_event_id', $event->id)->sole();

    expect($event->discipline_id)->toBe(TEST_SPRINT_RELAY_ID)
        ->and($team->relay_type)->toBe(RelayType::SprintRelay)
        ->and($team->members()->count())->toBe(3);
});

test('does not create a relay team for a past event', function (): void {
    $event = SportEvent::factory()->create([
        'oris_id' => 919286,
        'discipline_id' => TEST_MASS_START_ID,
        'date' => now()->subYear(),
    ]);
    fakeOrisDisciplines([919286 => TEST_SPRINT_RELAY_ID]);

    runDisciplineSync()->assertSuccessful();

    expect($event->refresh()->discipline_id)->toBe(TEST_SPRINT_RELAY_ID)
        ->and(RelayTeam::query()->where('sport_event_id', $event->id)->exists())->toBeFalse();
});

test('dry run changes nothing', function (): void {
    $event = SportEvent::factory()->create([
        'oris_id' => 919287,
        'discipline_id' => TEST_MASS_START_ID,
        'date' => now()->addMonth(),
    ]);
    fakeOrisDisciplines([919287 => TEST_SPRINT_RELAY_ID]);

    runDisciplineSync('--dry-run')->assertSuccessful();

    expect($event->refresh()->discipline_id)->toBe(TEST_MASS_START_ID)
        ->and(RelayTeam::query()->where('sport_event_id', $event->id)->exists())->toBeFalse();
});

test('skips events with an ORIS error or an unknown discipline and fails', function (): void {
    $errored = SportEvent::factory()->create(['oris_id' => 919288, 'discipline_id' => TEST_MASS_START_ID]);
    $unknown = SportEvent::factory()->create(['oris_id' => 919289, 'discipline_id' => TEST_MASS_START_ID]);
    fakeOrisDisciplines([919288 => null, 919289 => 999999]);

    runDisciplineSync()->assertFailed();

    expect($errored->refresh()->discipline_id)->toBe(TEST_MASS_START_ID)
        ->and($unknown->refresh()->discipline_id)->toBe(TEST_MASS_START_ID);
});

test('leaves matching disciplines untouched', function (): void {
    $event = SportEvent::factory()->create(['oris_id' => 919290, 'discipline_id' => TEST_MASS_START_ID]);
    fakeOrisDisciplines([919290 => TEST_MASS_START_ID]);

    runDisciplineSync()
        ->expectsOutputToContain('All disciplines match ORIS.')
        ->assertSuccessful();

    expect($event->refresh()->discipline_id)->toBe(TEST_MASS_START_ID);
});
