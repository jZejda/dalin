<?php

declare(strict_types=1);

test('the user overview page shows the release, the build and the runtime', function (): void {
    actingAsSuperAdmin();

    config()->set('version.version', '13.1.0');

    $this->followingRedirects()
        ->get('/admin/user-overview')
        ->assertOk()
        ->assertSee(__('dashboard.version.heading'))
        ->assertSee('v13.1.0')
        ->assertSee('Laravel');
});
