<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Pages\ClubSettings;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
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

it('saves public website SEO settings and replaces the sharing image', function (): void {
    actingAsSuperAdmin();
    Storage::fake('public');
    Storage::disk('public')->putFileAs('seo', UploadedFile::fake()->image('old.jpg', 1200, 630), 'old.jpg');
    Storage::disk('public')->putFileAs('seo', UploadedFile::fake()->image('new.jpg', 1200, 630), 'new.jpg');
    Storage::disk('public')->putFileAs('seo', UploadedFile::fake()->image('logo.png', 256, 256), 'logo.png');
    AppSetting::set(AppSetting::SEO_IMAGE, 'seo/old.jpg');

    Livewire::test(ClubSettings::class)
        ->assertSet('seo_image', fn (mixed $state): bool => is_array($state) && in_array('seo/old.jpg', $state, true))
        ->set('seo_description', '  Oddíl orientačního běhu z Testova.  ')
        ->set('seo_image', ['new' => 'seo/new.jpg'])
        ->set('seo_logo', ['logo' => 'seo/logo.png'])
        ->set('seo_same_as', ['https://www.facebook.com/oktestov'])
        ->call('submit')
        ->assertHasNoErrors();

    expect(AppSetting::getSeoDescription())->toBe('Oddíl orientačního běhu z Testova.')
        ->and(AppSetting::getSeoImagePath())->toBe('seo/new.jpg')
        ->and(AppSetting::getSeoLogoPath())->toBe('seo/logo.png')
        ->and(AppSetting::getSeoSameAs())->toBe(['https://www.facebook.com/oktestov'])
        ->and(Storage::disk('public')->exists('seo/old.jpg'))->toBeFalse();
});

it('ignores a sharing image path outside the SEO upload directory', function (): void {
    actingAsSuperAdmin();
    Storage::fake('public');
    Storage::disk('public')->put('avatars/1/avatar.jpg', 'x');

    Livewire::test(ClubSettings::class)
        ->set('seo_image', ['forged' => 'avatars/1/avatar.jpg'])
        ->set('seo_logo', ['forged' => 'seo/../avatars/1/avatar.jpg'])
        ->call('submit')
        ->assertHasNoErrors();

    expect(AppSetting::getSeoImagePath())->toBeNull()
        ->and(AppSetting::getSeoLogoPath())->toBeNull()
        ->and(Storage::disk('public')->exists('avatars/1/avatar.jpg'))->toBeTrue();
});

it('rejects social profiles that are not urls', function (): void {
    actingAsSuperAdmin();

    Livewire::test(ClubSettings::class)
        ->set('seo_same_as', ['facebook oktestov'])
        ->call('submit')
        ->assertHasErrors(['seo_same_as.0']);
});

it('saves the club accent colour and clears it when emptied', function (): void {
    actingAsSuperAdmin();

    $fillRequired = fn ($component) => $component
        ->set('full_name', 'Orientační klub Testov')
        ->set('primary_bank_account_number', '987654321/0100')
        ->set('primary_bank_account_name', 'Komerční banka')
        ->set('user_credit_limit', '-500')
        ->set('regular_membership_fees_prefix', '222')
        ->set('extra_membership_fees_prefix', '999');

    $fillRequired(Livewire::test(ClubSettings::class))
        ->set('accent_color', '#1F7A5C')
        ->call('submit')
        ->assertHasNoErrors();

    expect(AppSetting::getBrandingAccentColor())->toBe('#1f7a5c');

    $fillRequired(Livewire::test(ClubSettings::class))
        ->set('accent_color', null)
        ->call('submit')
        ->assertHasNoErrors();

    expect(AppSetting::getBrandingAccentColor())->toBeNull();
});

it('rejects an accent colour that is not a hex value', function (): void {
    actingAsSuperAdmin();

    Livewire::test(ClubSettings::class)
        ->set('accent_color', 'red')
        ->call('submit')
        ->assertHasErrors(['accent_color']);
});
