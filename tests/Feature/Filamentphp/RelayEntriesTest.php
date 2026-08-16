<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
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

test('relay event creates default team with three slots', function (): void {
    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'RE',
        'long_name' => 'Štafety',
        'relays' => true,
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    $team = RelayTeam::query()->where('sport_event_id', $event->id)->first();

    expect($team)->not()->toBeNull()
        ->and($team?->slots_count)->toBe(3)
        ->and($team?->members()->count())->toBe(3);
});

test('non relay event does not create default relay team', function (): void {
    $normalDiscipline = SportDiscipline::query()->create([
        'short_name' => 'KT',
        'long_name' => 'Krátká trať',
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $normalDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    expect(RelayTeam::query()->where('sport_event_id', $event->id)->count())->toBe(0);
});

test('relay team extends member slots when slots count increases', function (): void {
    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'TE',
        'long_name' => 'Družstva',
        'relays' => true,
    ]);

    $event = SportEvent::factory()->create([
        'discipline_id' => $relayDiscipline->id,
        'use_oris_for_entries' => false,
    ]);

    $team = RelayTeam::query()->where('sport_event_id', $event->id)->firstOrFail();
    $team->slots_count = 4;
    $team->saveOrFail();

    expect($team->members()->count())->toBe(4);
});

test('relay event keeps already entered profile available for another team slot', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $relayDiscipline = SportDiscipline::query()->create([
        'short_name' => 'SR',
        'long_name' => 'Sprintové štafety',
        'relays' => true,
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
