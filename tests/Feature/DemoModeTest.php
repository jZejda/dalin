<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;

// ---------------------------------------------------------------------------
// demo:reset guard
// ---------------------------------------------------------------------------

test('demo:reset command refuses to run when demo mode is disabled', function (): void {
    config(['demo.enabled' => false]);

    $this->artisan('demo:reset')
        ->expectsOutputToContain('Demo mode is disabled')
        ->assertExitCode(1);
});

// ---------------------------------------------------------------------------
// /demo-reset/{key} endpoint
// ---------------------------------------------------------------------------

test('demo reset endpoint returns 404 when demo mode is disabled', function (): void {
    config(['demo.enabled' => false]);

    $this->get('/demo-reset/' . config('demo.reset_url_key'))
        ->assertNotFound();
});

test('demo reset endpoint triggers demo:reset when demo mode is enabled', function (): void {
    config(['demo.enabled' => true]);

    Artisan::shouldReceive('call')
        ->once()
        ->with('demo:reset')
        ->andReturn(0);

    $this->get('/demo-reset/' . config('demo.reset_url_key'))
        ->assertOk();
});

test('demo reset endpoint returns 404 for wrong key', function (): void {
    config(['demo.enabled' => true]);

    $this->get('/demo-reset/definitely-wrong-key')
        ->assertNotFound();
});

// ---------------------------------------------------------------------------
// Demo banner and login credentials
// ---------------------------------------------------------------------------

test('login page shows demo credentials and banner when demo mode is enabled', function (): void {
    config(['demo.enabled' => true]);

    $this->get('/admin/login')
        ->assertOk()
        ->assertSee('admin@demo.cz')
        ->assertSee('member@demo.cz')
        ->assertSee('DEMO režim');
});

test('login page hides demo credentials and banner when demo mode is disabled', function (): void {
    config(['demo.enabled' => false]);

    $this->get('/admin/login')
        ->assertOk()
        ->assertDontSee('admin@demo.cz')
        ->assertDontSee('DEMO režim');
});
