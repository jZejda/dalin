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
