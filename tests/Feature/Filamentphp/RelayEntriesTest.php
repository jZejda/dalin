<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
use App\Filament\Resources\SportEvents\Pages\EntrySportEvent;
use App\Models\RelayTeam;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->user = User::factory()->create(['active' => true]);
    $this->raceProfile = UserRaceProfile::create([
        'user_id' => $this->user->id,
        'first_name' => 'Relay',
        'last_name' => 'Runner',
        'reg_number' => 'RLY1001',
        'gender' => 'M',
        'active' => true,
    ]);
});

test('relay event does not create any default team', function (): void {
    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'ST',
        'long_name' => 'Štafety',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    expect(RelayTeam::query()->where('sport_event_id', $event->id)->count())->toBe(0);
});

test('creating relay team creates member slot rows', function (): void {
    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'ST',
        'long_name' => 'Štafety',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    $team = RelayTeam::query()->create([
        'sport_event_id' => $event->id,
        'name' => 'Štafeta 1',
        'relay_type' => 'ST',
        'slots_count' => 3,
    ]);

    expect($team->members()->count())->toBe(3)
        ->and($team->members()->pluck('slot')->all())->toBe([1, 2, 3]);
});

test('relay team extends member slots when slots count increases', function (): void {
    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'DR',
        'long_name' => 'Družstva',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    $team = RelayTeam::query()->create([
        'sport_event_id' => $event->id,
        'name' => 'Družstvo 1',
        'relay_type' => 'DR',
        'slots_count' => 3,
    ]);

    $team->slots_count = 4;
    $team->saveOrFail();

    expect($team->members()->count())->toBe(4);
});

test('relay profiles are keyed by internal id even for oris relay event', function (): void {
    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'ST',
        'long_name' => 'Štafety',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'oris_id' => 9999,
        'use_oris_for_entries' => true,
    ]);

    $this->raceProfile->update(['oris_id' => 555555]);

    $this->actingAs($this->user);
    $profiles = (new UserRaceProfiles())->getUserRaceProfiles($event);

    expect($profiles->keys()->contains($this->raceProfile->id))->toBeTrue()
        ->and($profiles->keys()->contains(555555))->toBeFalse();
});

test('same profile cannot be entered twice into one relay team but can join another team', function (): void {
    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'ST',
        'long_name' => 'Štafety',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    $classDefinition = SportClassDefinition::query()->create([
        'sport_id' => 1,
        'age_from' => 18,
        'age_to' => 40,
        'gender' => 'M',
        'name' => 'H21',
    ]);

    $sportClass = SportClass::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $classDefinition->id,
        'name' => 'H21',
    ]);

    $teamA = RelayTeam::query()->create([
        'sport_event_id' => $event->id,
        'sport_class_id' => $sportClass->id,
        'name' => 'Tým A',
        'relay_type' => 'ST',
        'slots_count' => 2,
    ]);

    $teamB = RelayTeam::query()->create([
        'sport_event_id' => $event->id,
        'sport_class_id' => $sportClass->id,
        'name' => 'Tým B',
        'relay_type' => 'ST',
        'slots_count' => 2,
    ]);

    $page = new EntrySportEvent();
    $page->record = $event;
    $storeRelayUserEntry = new ReflectionMethod($page, 'storeRelayUserEntry');

    $entryData = static fn (int $relayTeamMemberId): array => [
        'relayTeamMemberId' => $relayTeamMemberId,
        'note' => null,
        'club_note' => null,
        'requested_start' => null,
        'si' => null,
        'rent_si' => false,
    ];

    $slotA1 = $teamA->members()->where('slot', 1)->firstOrFail();
    $slotA2 = $teamA->members()->where('slot', 2)->firstOrFail();
    $slotB1 = $teamB->members()->where('slot', 1)->firstOrFail();

    $firstEntry = $storeRelayUserEntry->invoke($page, $event, $this->raceProfile, $entryData($slotA1->id));
    $duplicateInSameTeam = $storeRelayUserEntry->invoke($page, $event, $this->raceProfile, $entryData($slotA2->id));
    $entryInAnotherTeam = $storeRelayUserEntry->invoke($page, $event, $this->raceProfile, $entryData($slotB1->id));

    expect($firstEntry)->toBeTrue()
        ->and($duplicateInSameTeam)->toBeFalse()
        ->and($entryInAnotherTeam)->toBeTrue()
        ->and(UserEntry::query()->where('sport_event_id', $event->id)->count())->toBe(2)
        ->and($slotA2->refresh()->user_race_profile_id)->toBeNull();
});

test('relay event keeps already entered profile available for another team slot', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'SS',
        'long_name' => 'Sprintové štafety',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    $classDefinition = SportClassDefinition::query()->create([
        'sport_id' => 1,
        'age_from' => 18,
        'age_to' => 40,
        'gender' => 'M',
        'name' => 'H21',
    ]);

    SportClass::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $classDefinition->id,
        'name' => 'H21',
    ]);

    UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $classDefinition->id,
        'user_race_profile_id' => $this->raceProfile->id,
        'class_name' => 'H21',
        'entry_status' => EntryStatus::Create->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $this->actingAs($this->user);
    $profiles = (new UserRaceProfiles())->getUserRaceProfiles($event);

    expect($profiles->keys()->contains($this->raceProfile->id))->toBeTrue();
});

test('non relay event hides profile when already entered', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $normalDiscipline = SportDiscipline::query()->create([
        'short_name' => 'KT',
        'long_name' => 'Krátká trať',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $normalDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    $classDefinition = SportClassDefinition::query()->create([
        'sport_id' => 1,
        'age_from' => 18,
        'age_to' => 40,
        'gender' => 'M',
        'name' => 'H21',
    ]);

    SportClass::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $classDefinition->id,
        'name' => 'H21',
    ]);

    UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $classDefinition->id,
        'user_race_profile_id' => $this->raceProfile->id,
        'class_name' => 'H21',
        'entry_status' => EntryStatus::Create->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $this->actingAs($this->user);
    $profiles = (new UserRaceProfiles())->getUserRaceProfiles($event);

    expect($profiles->keys()->contains($this->raceProfile->id))->toBeFalse();
});
