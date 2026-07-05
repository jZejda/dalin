<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Pages\ClubSettings;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

beforeEach(function (): void {
    Cache::flush();
});

it('renders club settings page for super admin', function (): void {
    actingAsSuperAdmin();

    $this->get('/admin/config/club')->assertOk();
});

it('denies club settings page to plain member', function (): void {
    $member = User::factory()->create(['active' => true]);
    $this->actingAs($member);

    $this->get('/admin/config/club')->assertForbidden();
});

it('saves club settings and applies config overrides', function (): void {
    actingAsSuperAdmin();

    Livewire::test(ClubSettings::class)
        ->set('full_name', 'Orientační klub Testov')
        ->set('primary_bank_account_number', '987654321/0100')
        ->set('primary_bank_account_name', 'Komerční banka')
        ->set('iban', 'CZ6508000000192000145399')
        ->set('user_credit_limit', '-500')
        ->set('regular_membership_fees_prefix', '222')
        ->set('extra_membership_fees_prefix', '999')
        ->set('technical_email', 'tech@example.com')
        ->call('submit')
        ->assertHasNoErrors();

    expect(AppSetting::get(AppSetting::CLUB_FULL_NAME))->toBe('Orientační klub Testov')
        ->and(AppSetting::get(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NUMBER))->toBe('987654321/0100')
        ->and(AppSetting::get(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NAME))->toBe('Komerční banka')
        ->and(AppSetting::get(AppSetting::CLUB_IBAN))->toBe('CZ6508000000192000145399')
        ->and(AppSetting::get(AppSetting::CLUB_USER_CREDIT_LIMIT))->toBe(-500)
        ->and(AppSetting::get(AppSetting::CLUB_REGULAR_MEMBERSHIP_FEES_PREFIX))->toBe('222')
        ->and(AppSetting::get(AppSetting::CLUB_EXTRA_MEMBERSHIP_FEES_PREFIX))->toBe('999')
        ->and(AppSetting::get(AppSetting::CLUB_TECHNICAL_EMAIL))->toBe('tech@example.com')
        ->and(config('site-config.club.full_name'))->toBe('Orientační klub Testov')
        ->and(config('site-config.club.user_credit_limit'))->toBe(-500);
});

it('stores empty iban and technical email as null to keep file defaults', function (): void {
    actingAsSuperAdmin();

    AppSetting::set(AppSetting::CLUB_IBAN, 'CZ6508000000192000145399');
    AppSetting::set(AppSetting::CLUB_TECHNICAL_EMAIL, 'tech@example.com');

    Livewire::test(ClubSettings::class)
        ->set('full_name', 'Orientační klub Testov')
        ->set('primary_bank_account_number', '987654321/0100')
        ->set('primary_bank_account_name', 'Komerční banka')
        ->set('iban', '')
        ->set('user_credit_limit', '-500')
        ->set('regular_membership_fees_prefix', '222')
        ->set('extra_membership_fees_prefix', '999')
        ->set('technical_email', '')
        ->call('submit')
        ->assertHasNoErrors();

    expect(AppSetting::get(AppSetting::CLUB_IBAN))->toBeNull()
        ->and(AppSetting::get(AppSetting::CLUB_TECHNICAL_EMAIL))->toBeNull();
});

it('rejects positive credit limit and invalid fee prefix', function (): void {
    actingAsSuperAdmin();

    Livewire::test(ClubSettings::class)
        ->set('full_name', 'Orientační klub Testov')
        ->set('primary_bank_account_number', '987654321/0100')
        ->set('primary_bank_account_name', 'Komerční banka')
        ->set('user_credit_limit', '100')
        ->set('regular_membership_fees_prefix', 'abc')
        ->set('extra_membership_fees_prefix', '999')
        ->call('submit')
        ->assertHasErrors(['user_credit_limit', 'regular_membership_fees_prefix']);
});
