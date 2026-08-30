<?php

declare(strict_types=1);

use App\Filament\Clusters\Other\Pages\UserMailNotification;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = actingAsSuperAdmin();
});

test('unauthenticated user is redirected from user settings', function () {
    Auth::logout();

    $this->get('/admin/other/user-settings')
        ->assertRedirect();
});

test('authenticated user can access user settings page', function () {
    Livewire::test(UserMailNotification::class)
        ->assertStatus(200);
});

test('user without token has has_calendar_token set to false', function () {
    expect($this->user->calendar_token)->toBeNull();

    Livewire::test(UserMailNotification::class)
        ->assertSet('has_calendar_token', false);
});

test('generate calendar token stores hash in database', function () {
    Livewire::test(UserMailNotification::class)
        ->call('generateCalendarToken')
        ->assertSet('has_calendar_token', true);

    expect($this->user->fresh()->calendar_token)->not->toBeNull();
});

test('regenerate calendar token changes hash in database', function () {
    Livewire::test(UserMailNotification::class)
        ->call('generateCalendarToken');

    $originalHash = $this->user->fresh()->calendar_token;

    Livewire::test(UserMailNotification::class)
        ->call('regenerateCalendarToken')
        ->assertSet('has_calendar_token', true);

    $newHash = $this->user->fresh()->calendar_token;

    expect($newHash)->not->toBeNull()
        ->and($newHash)->not->toBe($originalHash);
});

test('revoke calendar token removes hash from database', function () {
    Livewire::test(UserMailNotification::class)
        ->call('generateCalendarToken');

    expect($this->user->fresh()->calendar_token)->not->toBeNull();

    Livewire::test(UserMailNotification::class)
        ->call('revokeCalendarToken')
        ->assertSet('has_calendar_token', false)
        ->assertSet('calendar_token', null);

    expect($this->user->fresh()->calendar_token)->toBeNull();
});

test('user with existing token sees calendar_token set on fresh page load', function () {
    $this->user->calendar_token = bin2hex(random_bytes(32));
    $this->user->save();

    Livewire::test(UserMailNotification::class)
        ->assertSet('has_calendar_token', true)
        ->assertSet('calendar_token', $this->user->calendar_token);
});
