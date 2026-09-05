<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Enums\BadgeColor;
use App\Filament\Clusters\Other\Pages\MyMarketOfferList;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\UserRaceProfileResource;
use App\Filament\Pages\UserRaceProfileList;
use App\Models\AppSetting;
use App\Models\MailLog;
use App\Models\MarketOffer;
use App\Models\User;
use App\Models\UserRaceProfile;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

it('renders a colored dot on the badge when a dot color is given', function (): void {
    expect(view('components.user-badge', ['initials' => 'AB', 'dotColor' => BadgeColor::Red])->render())
        ->toContain(BadgeColor::Red->shade());
});

it('does not render a dot on the badge when no dot color is given', function (): void {
    expect(view('components.user-badge', ['initials' => 'AB'])->render())
        ->not->toContain('-top-0.5 -right-0.5');
});

it('falls back to a neutral gray badge when there is no color', function (): void {
    expect(view('components.user-badge', ['initials' => 'N/A'])->render())
        ->toContain('N/A')
        ->toContain('oklch(0.551 0.027 264.364)');
});

it('shows a red dot on the full identity component for a deactivated user', function (): void {
    $user = User::factory()->create(['active' => false]);

    expect(view('components.user-identity', ['user' => $user])->render())
        ->toContain(BadgeColor::Red->shade());
});

it('does not show a dot on the full identity component for an active user', function (): void {
    $user = User::factory()->create(['active' => true]);

    expect(view('components.user-identity', ['user' => $user])->render())
        ->not->toContain('-top-0.5 -right-0.5');
});

it('renders the user race profile list with an active and an inactive account', function (): void {
    actingAsSuperAdmin();

    $activeUser = User::factory()->create(['active' => true, 'name' => 'Aktivní Uživatel']);
    $inactiveUser = User::factory()->create(['active' => false, 'name' => 'Neaktivní Uživatel']);

    UserRaceProfile::factory()->create(['user_id' => $activeUser->id]);
    UserRaceProfile::factory()->create(['user_id' => $inactiveUser->id]);

    $this->get(UserRaceProfileResource::getUrl('index'))
        ->assertOk()
        ->assertSee('Aktivní Uživatel')
        ->assertSee('Neaktivní Uživatel');
});

it('renders the admin user race profile list with the full identity column', function (): void {
    actingAsSuperAdmin();

    $activeUser = User::factory()->create(['active' => true, 'name' => 'Aktivní Uživatel']);
    $inactiveUser = User::factory()->create(['active' => false, 'name' => 'Neaktivní Uživatel']);

    UserRaceProfile::factory()->create(['user_id' => $activeUser->id]);
    UserRaceProfile::factory()->create(['user_id' => $inactiveUser->id]);

    $this->get(UserRaceProfileList::getUrl())
        ->assertOk()
        ->assertSee('Aktivní Uživatel')
        ->assertSee($activeUser->email)
        ->assertSee('Neaktivní Uživatel')
        ->assertSee($inactiveUser->email)
        ->assertSee(BadgeColor::Red->shade());
});

it('shows a neutral N/A badge for a system mail log with no source user', function (): void {
    actingAsSuperAdmin();

    MailLog::factory()->create(['source_user_id' => null]);

    $this->get('/admin/config/mail-logs')
        ->assertOk()
        ->assertSee('N/A');
});

it('renders the market offer list with the compact author column wired up', function (): void {
    // The author column is toggleable and hidden by default, so it isn't necessarily present
    // in the initial HTML; this just confirms the ViewColumn wiring doesn't break the page.
    AppSetting::set(AppSetting::MARKETPLACE_MODULE_ENABLED, true);

    $member = User::factory()->create(['active' => true]);
    $member->assignRole(AppRoles::Member->value);
    $this->actingAs($member);

    $offer = MarketOffer::factory()->create(['user_id' => $member->id]);

    $this->get(MyMarketOfferList::getUrl())
        ->assertOk()
        ->assertSee($offer->title);
});
