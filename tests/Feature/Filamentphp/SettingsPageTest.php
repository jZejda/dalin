<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Pages\Settings;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

beforeEach(function (): void {
    Cache::flush();
});

it('saves marketplace module toggle', function (): void {
    actingAsSuperAdmin();

    expect(AppSetting::isMarketplaceModuleEnabled())->toBeFalse();

    Livewire::test(Settings::class)
        ->set('marketplace_enabled', true)
        ->call('submit')
        ->assertHasNoErrors();

    Cache::flush();

    expect(AppSetting::isMarketplaceModuleEnabled())->toBeTrue();
});

it('loads saved module toggles on mount', function (): void {
    actingAsSuperAdmin();

    AppSetting::set(AppSetting::MARKETPLACE_MODULE_ENABLED, true);

    Livewire::test(Settings::class)
        ->assertSet('marketplace_enabled', true);
});

it('saves the mapy.cz toggle and api key', function (): void {
    actingAsSuperAdmin();

    expect(AppSetting::isMapyLayerActive())->toBeFalse();

    Livewire::test(Settings::class)
        ->set('mapy_enabled', true)
        ->set('mapy_api_key', 'secret-api-key')
        ->call('submit')
        ->assertHasNoErrors();

    Cache::flush();

    expect(AppSetting::isMapyModuleEnabled())->toBeTrue()
        ->and(AppSetting::getMapyApiKey())->toBe('secret-api-key');
});

it('requires an api key when enabling mapy.cz for the first time', function (): void {
    actingAsSuperAdmin();

    Livewire::test(Settings::class)
        ->set('mapy_enabled', true)
        ->set('mapy_api_key', '')
        ->call('submit')
        ->assertHasErrors(['mapy_api_key' => 'required']);
});

it('keeps the saved mapy.cz api key when the field is left blank', function (): void {
    actingAsSuperAdmin();

    AppSetting::set(AppSetting::MAPY_MODULE_ENABLED, true);
    AppSetting::setMapyApiKey('existing-api-key');

    Livewire::test(Settings::class)
        ->set('mapy_api_key', '')
        ->call('submit')
        ->assertHasNoErrors();

    Cache::flush();

    expect(AppSetting::getMapyApiKey())->toBe('existing-api-key');
});
