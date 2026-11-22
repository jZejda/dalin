<?php

declare(strict_types=1);

use App\Enums\SiChipSport;
use App\Enums\SiChipType;
use App\Models\UserRaceProfile;
use App\Models\UserRaceProfileChip;

it('defaults the primary chip to all sports and all types', function (): void {
    $profile = UserRaceProfile::factory()->create(['si' => 111111])->refresh();

    expect($profile->si_sport)->toBe(SiChipSport::All)
        ->and($profile->si_type)->toBe(SiChipType::All);
});

it('returns the primary chip when there are no extra chips', function (): void {
    $profile = UserRaceProfile::factory()->create(['si' => 111111]);

    expect($profile->siForSport(1))->toBe(111111)
        ->and($profile->siForSport(null))->toBe(111111);
});

it('prefers a chip dedicated to the event sport', function (): void {
    $profile = UserRaceProfile::factory()->create(['si' => 111111]);
    UserRaceProfileChip::factory()->forSport(SiChipSport::Mtbo)->create([
        'user_race_profile_id' => $profile->id,
        'si' => 222222,
    ]);

    expect($profile->refresh()->siForSport(SiChipSport::Mtbo->value))->toBe(222222)
        ->and($profile->siForSport(SiChipSport::Ob->value))->toBe(111111);
});

it('falls back to an all-sports chip before a chip of another sport', function (): void {
    $profile = UserRaceProfile::factory()->create(['si' => 111111, 'si_sport' => SiChipSport::Lob]);
    UserRaceProfileChip::factory()->create([
        'user_race_profile_id' => $profile->id,
        'si' => 333333,
        'sport' => SiChipSport::All,
    ]);

    expect($profile->refresh()->siForSport(SiChipSport::Ob->value))->toBe(333333);
});

it('uses the primary chip when no chip matches the sport', function (): void {
    $profile = UserRaceProfile::factory()->create(['si' => 111111, 'si_sport' => SiChipSport::Lob]);

    expect($profile->siForSport(SiChipSport::Ob->value))->toBe(111111);
});

it('returns null when the profile has no chip at all', function (): void {
    $profile = UserRaceProfile::factory()->create(['si' => null]);

    expect($profile->siForSport(1))->toBeNull();
});

it('deletes extra chips together with the profile', function (): void {
    $profile = UserRaceProfile::factory()->create();
    UserRaceProfileChip::factory()->create(['user_race_profile_id' => $profile->id]);

    $profile->delete();

    expect(UserRaceProfileChip::query()->where('user_race_profile_id', $profile->id)->count())->toBe(0);
});

describe('profile form', function (): void {
    beforeEach(function (): void {
        actingAsSuperAdmin();
    });

    it('saves the primary chip sport and extra chips', function (): void {
        // The form offers H/D while the factory generates M/F
        $profile = UserRaceProfile::factory()->create(['si' => 111111, 'gender' => 'H']);

        Livewire\Livewire::test(App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages\EditUserRaceProfile::class, ['record' => $profile->getKey()])
            ->fillForm([
                'si_sport' => SiChipSport::Ob->value,
                'chips' => [
                    ['si' => 222222, 'sport' => SiChipSport::Mtbo->value, 'type' => SiChipType::Contactless->value],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $profile->refresh();

        expect($profile->si_sport)->toBe(SiChipSport::Ob)
            ->and($profile->chips)->toHaveCount(1)
            ->and($profile->chips->first()->si)->toBe(222222)
            ->and($profile->chips->first()->type)->toBe(SiChipType::Contactless)
            ->and($profile->siForSport(SiChipSport::Mtbo->value))->toBe(222222);
    });

    it('allows at most two extra chips', function (): void {
        $profile = UserRaceProfile::factory()->create();

        Livewire\Livewire::test(App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages\EditUserRaceProfile::class, ['record' => $profile->getKey()])
            ->fillForm([
                'chips' => [
                    ['si' => 1001, 'sport' => 0, 'type' => 0],
                    ['si' => 1002, 'sport' => 0, 'type' => 0],
                    ['si' => 1003, 'sport' => 0, 'type' => 0],
                ],
            ])
            ->call('save')
            ->assertHasFormErrors(['chips']);
    });
});
