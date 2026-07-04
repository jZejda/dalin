<?php

declare(strict_types=1);

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

it('returns default when key is missing', function (): void {
    expect(AppSetting::get('missing.key'))->toBeNull()
        ->and(AppSetting::get('missing.key', 'fallback'))->toBe('fallback')
        ->and(AppSetting::boolean('missing.key'))->toBeFalse()
        ->and(AppSetting::boolean('missing.key', true))->toBeTrue();
});

it('stores and retrieves scalar and array values', function (): void {
    AppSetting::set('some.flag', true);
    AppSetting::set('some.text', 'hodnota');
    AppSetting::set('some.list', ['a' => 1, 'b' => 2]);

    expect(AppSetting::get('some.flag'))->toBeTrue()
        ->and(AppSetting::get('some.text'))->toBe('hodnota')
        ->and(AppSetting::get('some.list'))->toBe(['a' => 1, 'b' => 2]);
});

it('refreshes cached value after update', function (): void {
    AppSetting::set('some.flag', true);
    expect(AppSetting::boolean('some.flag'))->toBeTrue();

    AppSetting::set('some.flag', false);
    expect(AppSetting::boolean('some.flag'))->toBeFalse();
});

it('overrides club config with values saved in app settings', function (): void {
    config()->set('site-config.club.full_name', 'KLUB ABC');
    config()->set('site-config.club.user_credit_limit', -2000);

    AppSetting::set(AppSetting::CLUB_FULL_NAME, 'Orientační klub Testov');
    AppSetting::set(AppSetting::CLUB_USER_CREDIT_LIMIT, -500);

    AppSetting::applyClubConfigOverrides();

    expect(config('site-config.club.full_name'))->toBe('Orientační klub Testov')
        ->and(config('site-config.club.user_credit_limit'))->toBe(-500);
});

it('keeps config file defaults for club settings that were never saved', function (): void {
    config()->set('site-config.club.iban', 'CZ6508000000192000145399');
    config()->set('site-config.club.technical_email', 'tech@example.com');

    AppSetting::applyClubConfigOverrides();

    expect(config('site-config.club.iban'))->toBe('CZ6508000000192000145399')
        ->and(config('site-config.club.technical_email'))->toBe('tech@example.com');
});

it('reports transport module state', function (): void {
    expect(AppSetting::isTransportModuleEnabled())->toBeFalse();

    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
    expect(AppSetting::isTransportModuleEnabled())->toBeTrue();

    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, false);
    expect(AppSetting::isTransportModuleEnabled())->toBeFalse();
});
