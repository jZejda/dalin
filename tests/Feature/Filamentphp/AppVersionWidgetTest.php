<?php

declare(strict_types=1);

use App\Filament\Widgets\AppVersion;

use function Pest\Livewire\livewire;

test('the version widget shows the release, the build and the runtime', function (): void {
    actingAsSuperAdmin();

    config()->set('version.version', '13.1.0');

    livewire(AppVersion::class)
        ->assertSee(__('dashboard.version.heading'))
        ->assertSee('v13.1.0')
        ->assertSee('Laravel');
});

test('the user overview page renders the version widget', function (): void {
    actingAsSuperAdmin();

    config()->set('version.version', '13.1.0');

    $this->followingRedirects()
        ->get('/admin/user-overview')
        ->assertOk()
        ->assertSee('v13.1.0');
});
