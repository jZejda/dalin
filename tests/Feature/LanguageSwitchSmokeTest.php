<?php

declare(strict_types=1);

use App\Models\User;

it('renders the language switch in the admin panel topbar', function () {
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    $response = $this->actingAs($user)->followingRedirects()->get('/admin');

    $response->assertSuccessful();
    expect($response->getContent())->toContain('language-switch');
});

it('persists the chosen locale on the user via the LocaleChanged event', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    event(new BezhanSalleh\LanguageSwitch\Events\LocaleChanged('en'));

    expect($user->fresh()->locale)->toBe('en');
});
