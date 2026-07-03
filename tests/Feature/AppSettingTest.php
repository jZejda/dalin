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

it('reports transport module state', function (): void {
    expect(AppSetting::isTransportModuleEnabled())->toBeFalse();

    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, true);
    expect(AppSetting::isTransportModuleEnabled())->toBeTrue();

    AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, false);
    expect(AppSetting::isTransportModuleEnabled())->toBeFalse();
});
