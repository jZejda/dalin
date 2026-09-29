<?php

declare(strict_types=1);

test('homepage redirects to the admin login when the site has no public frontend', function (): void {
    config(['site-config.features.public_site.use_public_site' => false]);

    $this->get('/')
        ->assertRedirect(route('filament.admin.auth.login'));
});

test('homepage renders the public frontend when enabled', function (): void {
    config(['site-config.features.public_site.use_public_site' => true]);

    $this->get('/')
        ->assertOk()
        ->assertViewIs('welcome');
});
