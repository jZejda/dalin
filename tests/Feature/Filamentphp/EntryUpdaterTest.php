<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\Pages\Actions\UpdateEntryAction;
use App\Http\Components\Oris\Response\CreateEntry;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Entries\EntryUpdater;
use App\Services\SportEvents\Entries\OrisEntryClient;
use App\Services\SportEvents\Entries\UpdateResult;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function fakeOrisUpdateClient(string $status, array &$capturedParams = []): OrisEntryClient
{
    return new class ($status, $capturedParams) extends OrisEntryClient {
        /** @param array<string, mixed> $capturedParams */
        public function __construct(
            private string $status,
            private array &$capturedParams,
        ) {
        }

        public function updateEntry(array $entryData, int $orisEntryId, SportEvent $sportEvent): CreateEntry
        {
            $this->capturedParams = ['entryData' => $entryData, 'orisEntryId' => $orisEntryId];

            return new CreateEntry(
                Method: 'updateEntry',
                Format: 'json',
                Status: $this->status,
                ExportCreated: '2026-07-05 00:00:00',
                Data: null,
            );
        }
    };
}

beforeEach(function (): void {
    $this->user = User::factory()->create(['active' => true]);

    $this->raceProfile = UserRaceProfile::query()->create([
        'user_id' => $this->user->id,
        'first_name' => 'Test',
        'last_name' => 'Runner',
        'reg_number' => 'TST2001',
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

    $this->otherClassDefinition = SportClassDefinition::query()->create([
        'sport_id' => 1,
        'age_from' => 35,
        'age_to' => 99,
        'gender' => 'M',
        'name' => 'H35',
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

    $this->otherSportClass = SportClass::query()->create([
        'sport_event_id' => $this->event->id,
        'class_definition_id' => $this->otherClassDefinition->id,
        'name' => 'H35',
    ]);

    $this->entry = UserEntry::query()->create([
        'sport_event_id' => $this->event->id,
        'class_definition_id' => $this->classDefinition->id,
        'user_race_profile_id' => $this->raceProfile->id,
        'class_name' => 'H21',
        'note' => 'Původní poznámka',
        'si' => 11111,
        'rent_si' => false,
        'entry_status' => EntryStatus::Create->value,
        'entry_created' => now(),
    ]);

    $this->updateData = [
        'classId' => $this->otherSportClass->id,
        'si' => 22222,
        'rent_si' => 1,
        'note' => 'Nová poznámka',
        'club_note' => 'Nová klubová',
        'requested_start' => '(E0;pozde;)',
    ];
});

describe('EntryUpdater (non-ORIS)', function (): void {
    test('updates entry fields and sets status to Edit', function (): void {
        $result = EntryUpdater::make()->update($this->entry, $this->updateData);

        $this->entry->refresh();
        expect($result)->toBeInstanceOf(UpdateResult::class)
            ->and($result->success)->toBeTrue()
            ->and($result->wasOrisEntry)->toBeFalse()
            ->and($this->entry->class_definition_id)->toBe($this->otherClassDefinition->id)
            ->and($this->entry->class_name)->toBe('H35')
            ->and($this->entry->note)->toBe('Nová poznámka')
            ->and($this->entry->club_note)->toBe('Nová klubová')
            ->and($this->entry->requested_start)->toBe('(E0;pozde;)')
            ->and($this->entry->si)->toBe(22222)
            ->and($this->entry->rent_si)->toBeTrue()
            ->and($this->entry->entry_status)->toBe(EntryStatus::Edit);
    });

    test('fails when class does not exist for the event', function (): void {
        $data = $this->updateData;
        $data['classId'] = 999999;

        $result = EntryUpdater::make()->update($this->entry, $data);

        $this->entry->refresh();
        expect($result->success)->toBeFalse()
            ->and($this->entry->note)->toBe('Původní poznámka')
            ->and($this->entry->entry_status)->toBe(EntryStatus::Create);
    });

    test('fails when class belongs to a different event', function (): void {
        $otherEvent = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => null,
            'stages' => null,
            'sport_id' => 1,
        ]);
        $foreignClass = SportClass::query()->create([
            'sport_event_id' => $otherEvent->id,
            'class_definition_id' => $this->classDefinition->id,
            'name' => 'H21',
        ]);

        $data = $this->updateData;
        $data['classId'] = $foreignClass->id;

        $result = EntryUpdater::make()->update($this->entry, $data);

        expect($result->success)->toBeFalse();
    });

    test('updates entry_stages when provided', function (): void {
        $this->event->stages = 3;
        $this->event->saveOrFail();

        $data = $this->updateData + ['entry_stages' => ['stage1', 'stage3']];
        $result = EntryUpdater::make()->update($this->entry->fresh(), $data);

        expect($result->success)->toBeTrue()
            ->and($this->entry->fresh()->entry_stages)->toBe(['stage1', 'stage3']);
    });
});

describe('EntryUpdater (ORIS)', function (): void {
    beforeEach(function (): void {
        $this->event->oris_id = 8000;
        $this->event->use_oris_for_entries = true;
        $this->event->saveOrFail();

        $this->sportClass->oris_id = 501;
        $this->sportClass->saveOrFail();
        $this->otherSportClass->oris_id = 502;
        $this->otherSportClass->saveOrFail();

        $this->entry->oris_entry_id = 7777;
        $this->entry->saveOrFail();
    });

    test('pushes update to ORIS and persists locally on OK', function (): void {
        $captured = [];
        $updater = new EntryUpdater(fakeOrisUpdateClient('OK', $captured));

        $data = $this->updateData;
        $data['classId'] = 502; // ORIS class ID

        $result = $updater->update($this->entry->fresh(), $data);

        $this->entry->refresh();
        expect($result->success)->toBeTrue()
            ->and($result->wasOrisEntry)->toBeTrue()
            ->and($result->orisEventId)->toBe(8000)
            ->and($captured['orisEntryId'])->toBe(7777)
            ->and($this->entry->class_definition_id)->toBe($this->otherClassDefinition->id)
            ->and($this->entry->class_name)->toBe('H35')
            ->and($this->entry->entry_status)->toBe(EntryStatus::Edit);
    });

    test('does not persist local changes when ORIS rejects the update', function (): void {
        $captured = [];
        $updater = new EntryUpdater(fakeOrisUpdateClient('Error - entry not found', $captured));

        $data = $this->updateData;
        $data['classId'] = 502;

        $result = $updater->update($this->entry->fresh(), $data);

        $this->entry->refresh();
        expect($result->success)->toBeFalse()
            ->and($result->wasOrisEntry)->toBeTrue()
            ->and($result->orisStatusError)->toBe('Error - entry not found')
            ->and($this->entry->note)->toBe('Původní poznámka')
            ->and($this->entry->class_name)->toBe('H21')
            ->and($this->entry->entry_status)->toBe(EntryStatus::Create);
    });

    test('does not call ORIS when class ID is unknown', function (): void {
        $captured = [];
        $updater = new EntryUpdater(fakeOrisUpdateClient('OK', $captured));

        $data = $this->updateData;
        $data['classId'] = 999999;

        $result = $updater->update($this->entry->fresh(), $data);

        expect($result->success)->toBeFalse()
            ->and($captured)->toBe([]);
    });
});

describe('UpdateEntryAction::shouldHide', function (): void {
    beforeEach(function (): void {
        foreach (['member', 'event_master', 'event_organizer'] as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    });

    test('hidden for relay events even for EventMaster', function (): void {
        $discipline = new SportDiscipline();
        $discipline->short_name = 'RE';
        $discipline->long_name = 'Štafety update';
        $discipline->relays = true;
        $discipline->saveOrFail();

        $relayEvent = SportEvent::factory()->create([
            'use_oris_for_entries' => false,
            'oris_id' => null,
            'cancelled' => false,
            'discipline_id' => $discipline->id,
            'sport_id' => 1,
        ]);
        $this->entry->sport_event_id = $relayEvent->id;
        $this->entry->saveOrFail();

        $eventMaster = User::factory()->create(['active' => true]);
        $eventMaster->assignRole('event_master');
        $this->actingAs($eventMaster);

        expect((new UpdateEntryAction())->shouldHide($this->entry->fresh()))->toBeTrue();
    });

    test('hidden for cancelled entries', function (): void {
        $this->entry->entry_status = EntryStatus::Cancel;
        $this->entry->saveOrFail();

        $eventMaster = User::factory()->create(['active' => true]);
        $eventMaster->assignRole('event_master');
        $this->actingAs($eventMaster);

        expect((new UpdateEntryAction())->shouldHide($this->entry->fresh()))->toBeTrue();
    });

    test('visible for EventMaster regardless of ownership', function (): void {
        $eventMaster = User::factory()->create(['active' => true]);
        $eventMaster->assignRole('event_master');
        $this->actingAs($eventMaster);

        expect((new UpdateEntryAction())->shouldHide($this->entry))->toBeFalse();
    });

    test('visible for owner of the race profile', function (): void {
        $this->user->assignRole('member');
        $this->actingAs($this->user);

        expect((new UpdateEntryAction())->shouldHide($this->entry))->toBeFalse();
    });

    test('hidden for unrelated user', function (): void {
        $otherUser = User::factory()->create(['active' => true]);
        $otherUser->assignRole('member');
        $this->actingAs($otherUser);

        expect((new UpdateEntryAction())->shouldHide($this->entry))->toBeTrue();
    });
});
