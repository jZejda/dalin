<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Http\Controllers\Cron\Jobs\SyncEventStartLists;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\OrisApiService;
use Illuminate\Support\Facades\Http;

function startListEntryFor(SportEvent $event, string $regNumber, string $className, EntryStatus $status = EntryStatus::Create): UserEntry
{
    $profile = UserRaceProfile::query()->where('reg_number', $regNumber)->first()
        ?? UserRaceProfile::factory()->create(['reg_number' => $regNumber]);

    $classDefinition = SportClassDefinition::query()->firstOrCreate(
        ['sport_id' => 1, 'name' => $className],
        ['age_from' => 0, 'age_to' => 99, 'gender' => 'M'],
    );

    return UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $classDefinition->id,
        'user_race_profile_id' => $profile->id,
        'class_name' => $className,
        'entry_status' => $status->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);
}

/**
 * @return array<string, string|null>
 */
function orisStartListRow(string $id, string $classDesc, string $regNo, string $startTime): array
{
    return [
        'ID' => $id,
        'ClassID' => $id,
        'ClassDesc' => $classDesc,
        'Name' => 'Test ' . $regNo,
        'RegNo' => $regNo,
        'Lic' => null,
        'ClubNameStartLists' => null,
        'StartTime' => $startTime,
        'UserID' => null,
        'ClubID' => null,
        'StartNumber' => null,
        'SI' => null,
    ];
}

it('stores ORIS start times and leaves classes without a start time empty', function (): void {
    $event = SportEvent::factory()->create(['oris_id' => 990001]);
    $timed = startListEntryFor($event, 'XST1001', 'H21C');
    $untimed = startListEntryFor($event, 'XST1002', 'HDR');

    Http::fake([
        '*' => Http::response([
            'Method' => 'getEventStartLists',
            'Format' => 'json',
            'Status' => 'OK',
            'ExportCreated' => now()->toDateTimeString(),
            'Data' => [
                'Startlist_1' => orisStartListRow('1', 'H21C', 'XST1001', '2026-10-17 10:57:00'),
                // ORIS sends an empty string for classes without assigned start times
                'Startlist_2' => orisStartListRow('2', 'HDR', 'XST1002', ''),
            ],
        ]),
    ]);

    expect(new OrisApiService()->updateStartList($event->id))->toBeTrue();

    expect($timed->refresh()->real_start?->format('Y-m-d H:i:s'))->toBe('2026-10-17 10:57:00')
        ->and($untimed->refresh()->real_start)->toBeNull();
});

it('does not write the ORIS start time to a cancelled entry', function (): void {
    $event = SportEvent::factory()->create(['oris_id' => 990002]);
    $cancelled = startListEntryFor($event, 'XST2001', 'H21C', EntryStatus::Cancel);
    $active = startListEntryFor($event, 'XST2001', 'H21C', EntryStatus::Edit);

    Http::fake([
        '*' => Http::response([
            'Method' => 'getEventStartLists',
            'Format' => 'json',
            'Status' => 'OK',
            'ExportCreated' => now()->toDateTimeString(),
            'Data' => [
                'Startlist_1' => orisStartListRow('1', 'H21C', 'XST2001', '2026-10-17 10:57:00'),
            ],
        ]),
    ]);

    new OrisApiService()->updateStartList($event->id);

    expect($active->refresh()->real_start?->format('Y-m-d H:i:s'))->toBe('2026-10-17 10:57:00')
        ->and($cancelled->refresh()->real_start)->toBeNull();
});

it('syncs the start list only for events with an active entry still waiting for a start time', function (EntryStatus $status, bool $synced): void {
    $event = SportEvent::factory()->create([
        'oris_id' => 990003,
        'use_oris_for_entries' => true,
        'date' => now()->addDays(2)->toDateString(),
    ]);
    startListEntryFor($event, 'XST3001', 'H21C', $status);

    Http::fake(['*' => Http::response(['Status' => 'OK', 'Data' => []])]);

    new SyncEventStartLists()->run();

    expect(Http::recorded())->toHaveCount($synced ? 1 : 0);
})->with([
    'created entry' => [EntryStatus::Create, true],
    'edited entry' => [EntryStatus::Edit, true],
    'cancelled entry' => [EntryStatus::Cancel, false],
]);
