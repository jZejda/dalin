<?php

declare(strict_types=1);

use App\Enums\SiChipSport;
use App\Enums\SiChipType;
use App\Enums\SiSyncStatus;
use App\Filament\Clusters\Config\Pages\SiChipOverview;
use App\Models\User;
use App\Models\UserRaceProfile;
use App\Models\UserRaceProfileChip;
use App\Services\SiChips\OrisClubMember;
use App\Services\SiChips\OrisSiClient;
use App\Services\SiChips\OrisSiException;
use App\Services\SiChips\SiChipComparator;
use App\Services\SiChips\SiChipData;
use App\Services\SiChips\SiChipImporter;
use Livewire\Livewire;

function orisMember(int $userId, array $chips, string $regNum = 'ABM0001'): OrisClubMember
{
    return new OrisClubMember($userId, $userId + 1000, $regNum, $chips);
}

function fakeOris(array $members): void
{
    $client = Mockery::mock(OrisSiClient::class);
    $client->shouldReceive('clubMembers')->andReturn($members);
    $client->shouldReceive('forget');
    app()->instance(OrisSiClient::class, $client);
}

describe('ORIS payload parsing', function (): void {
    it('parses chip slots, skipping empty ones', function (): void {
        $members = (new OrisSiClient())->parseMembers([
            'ClubMember_5' => [
                'ID' => '5', 'UserID' => '77', 'RegNum' => 'ABM0005',
                'SI' => '332964', 'SISport' => 0, 'SIType' => 0,
                'SI2' => '', 'SISport2' => 0, 'SIType2' => 0,
                'SI3' => '8000123', 'SISport3' => '3', 'SIType3' => 2,
            ],
        ]);

        expect($members)->toHaveKey(77)
            ->and($members[77]->clubUserId)->toBe(5)
            ->and($members[77]->regNum)->toBe('ABM0005')
            ->and($members[77]->chips)->toHaveCount(2)
            ->and($members[77]->chips[1]->si)->toBe(8000123)
            ->and($members[77]->chips[1]->sport)->toBe(SiChipSport::Mtbo)
            ->and($members[77]->chips[1]->type)->toBe(SiChipType::Contactless);
    });

    it('maps unknown sport and type values to "all"', function (): void {
        $members = (new OrisSiClient())->parseMembers([
            ['ID' => 1, 'UserID' => 1, 'SI' => '123', 'SISport' => 99, 'SIType' => 99],
        ]);

        expect($members[1]->chips[0]->sport)->toBe(SiChipSport::All)
            ->and($members[1]->chips[0]->type)->toBe(SiChipType::All);
    });

    it('fails without a club key', function (): void {
        config(['site-config.oris_credentials.general.clubkey' => null]);
        Cache::forget(OrisSiClient::CACHE_KEY);

        (new OrisSiClient())->clubMembers();
    })->throws(OrisSiException::class);
});

describe('comparison', function (): void {
    it('reports the status of a profile against ORIS', function (
        ?int $dalinSi,
        array $orisChips,
        SiSyncStatus $expected,
    ): void {
        $profile = UserRaceProfile::factory()->create(['si' => $dalinSi, 'oris_id' => '77']);
        $members = [77 => orisMember(77, $orisChips)];

        expect((new SiChipComparator())->compare($profile, $members)->status)->toBe($expected);
    })->with([
        'same chip' => [111, [new SiChipData(111)], SiSyncStatus::InSync],
        'both empty' => [null, [], SiSyncStatus::InSync],
        'different chip' => [111, [new SiChipData(222)], SiSyncStatus::Differs],
        'same chip, other sport' => [111, [new SiChipData(111, SiChipSport::Ob)], SiSyncStatus::Differs],
        'ORIS empty' => [111, [], SiSyncStatus::MissingInOris],
        'Dalin empty' => [null, [new SiChipData(222)], SiSyncStatus::MissingInDalin],
    ]);

    it('ignores the order of chips', function (): void {
        $profile = UserRaceProfile::factory()->create(['si' => 111, 'oris_id' => '77']);
        UserRaceProfileChip::factory()->create(['user_race_profile_id' => $profile->id, 'si' => 222]);

        $members = [77 => orisMember(77, [new SiChipData(222), new SiChipData(111)])];

        expect((new SiChipComparator())->compare($profile->refresh(), $members)->status)->toBe(SiSyncStatus::InSync);
    });

    it('matches a profile without ORIS id by registration number', function (): void {
        $profile = UserRaceProfile::factory()->create(['si' => 111, 'oris_id' => null, 'reg_number' => 'ABM0042']);
        $members = [9 => orisMember(9, [new SiChipData(111)], 'ABM0042')];

        expect((new SiChipComparator())->compare($profile, $members)->status)->toBe(SiSyncStatus::InSync);
    });

    it('marks profiles missing in ORIS as not linked and unreadable ORIS as unknown', function (): void {
        $profile = UserRaceProfile::factory()->create(['oris_id' => '404', 'reg_number' => 'ZZZ1']);

        expect((new SiChipComparator())->compare($profile, [])->status)->toBe(SiSyncStatus::NotLinked)
            ->and((new SiChipComparator())->compare($profile, null)->status)->toBe(SiSyncStatus::Unknown);
    });
});

describe('pull from ORIS', function (): void {
    it('replaces Dalin chips with the ORIS ones', function (): void {
        $profile = UserRaceProfile::factory()->create(['si' => 111]);
        UserRaceProfileChip::factory()->create(['user_race_profile_id' => $profile->id, 'si' => 999]);

        (new SiChipImporter())->pullFromOris($profile, orisMember(1, [
            new SiChipData(222, SiChipSport::Ob, SiChipType::Contact),
            new SiChipData(333, SiChipSport::Mtbo, SiChipType::Contactless),
        ]));

        $profile->refresh();

        expect($profile->si)->toBe(222)
            ->and($profile->si_sport)->toBe(SiChipSport::Ob)
            ->and($profile->si_type)->toBe(SiChipType::Contact)
            ->and($profile->chips)->toHaveCount(1)
            ->and($profile->chips->first()->si)->toBe(333)
            ->and($profile->chips->first()->sport)->toBe(SiChipSport::Mtbo);
    });

    it('never wipes Dalin chips when ORIS has none', function (): void {
        $profile = UserRaceProfile::factory()->create(['si' => 111]);

        (new SiChipImporter())->pullFromOris($profile, orisMember(1, []));

        expect($profile->refresh()->si)->toBe(111);
    });
});

describe('overview page', function (): void {
    it('renders for super admin and denies plain members', function (): void {
        fakeOris([]);

        $this->actingAs(User::factory()->create(['active' => true]))->get('/admin/config/si-chips')->assertForbidden();

        actingAsSuperAdmin();
        $this->get('/admin/config/si-chips')->assertOk();
    });

    it('shows chips from both systems and pulls ORIS chips after confirmation', function (): void {
        actingAsSuperAdmin();
        $profile = UserRaceProfile::factory()->create(['si' => 111, 'oris_id' => '77', 'active' => true]);
        fakeOris([77 => orisMember(77, [new SiChipData(222)])]);

        Livewire::test(SiChipOverview::class)
            ->searchTable($profile->reg_number)
            ->assertCanSeeTableRecords([$profile])
            ->assertTableActionVisible('pull_from_oris', $profile)
            ->callTableAction('pull_from_oris', $profile);

        expect($profile->refresh()->si)->toBe(222);
    });

    it('hides the pull action for profiles already in sync', function (): void {
        actingAsSuperAdmin();
        $profile = UserRaceProfile::factory()->create(['si' => 111, 'oris_id' => '77', 'active' => true]);
        fakeOris([77 => orisMember(77, [new SiChipData(111)])]);

        Livewire::test(SiChipOverview::class)
            ->searchTable($profile->reg_number)
            ->assertTableActionHidden('pull_from_oris', $profile);
    });

    it('filters differences only', function (): void {
        actingAsSuperAdmin();
        $same = UserRaceProfile::factory()->create(['si' => 111, 'oris_id' => '77', 'active' => true]);
        $differs = UserRaceProfile::factory()->create(['si' => 333, 'oris_id' => '78', 'active' => true]);
        fakeOris([
            77 => orisMember(77, [new SiChipData(111)]),
            78 => orisMember(78, [new SiChipData(444)], 'ABM0002'),
        ]);

        Livewire::test(SiChipOverview::class)
            ->filterTable('only_differences', true)
            ->assertCanSeeTableRecords([$differs])
            ->assertCanNotSeeTableRecords([$same]);
    });

    it('stays usable when ORIS cannot be read', function (): void {
        actingAsSuperAdmin();
        $profile = UserRaceProfile::factory()->create(['active' => true]);
        $client = Mockery::mock(OrisSiClient::class);
        $client->shouldReceive('clubMembers')->andThrow(new OrisSiException('boom'));
        app()->instance(OrisSiClient::class, $client);

        Livewire::test(SiChipOverview::class)
            ->searchTable($profile->reg_number)
            ->assertCanSeeTableRecords([$profile])
            ->assertTableActionHidden('pull_from_oris', $profile);
    });
});
