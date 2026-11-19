<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Livewire\SportEvent\RaceProfilePaymentList;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Livewire\Livewire;

beforeEach(function (): void {
    actingAsSuperAdmin();

    $user = User::factory()->create(['active' => true]);

    $this->raceProfile = UserRaceProfile::query()->create([
        'user_id' => $user->id,
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

    $this->createEntry = function (string $className, EntryStatus $status = EntryStatus::Create): void {
        UserEntry::query()->create([
            'sport_event_id' => $this->event->id,
            'class_definition_id' => $this->classDefinition->id,
            'user_race_profile_id' => $this->raceProfile->id,
            'class_name' => $className,
            'entry_status' => $status->value,
            'rent_si' => false,
            'entry_created' => now(),
        ]);
    };
});

it('lists distinct active categories as the column state', function (): void {
    ($this->createEntry)('H21');
    ($this->createEntry)('H21');
    ($this->createEntry)('H35');
    ($this->createEntry)('D21', EntryStatus::Cancel);

    Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event])
        ->assertTableColumnStateSet('active_categories', ['H21', 'H35'], $this->raceProfile);
});

it('renders categories as Filament badges instead of raw Tailwind markup', function (): void {
    ($this->createEntry)('H21');

    Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event])
        ->assertSeeHtml('fi-badge')
        ->assertSee('H21')
        ->assertDontSeeHtml('dark:bg-blue-900');
});
