<?php

declare(strict_types=1);

use App\Enums\BadgeColor;
use App\Filament\Clusters\Other\Pages\UserProfileSettings;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('the application returns a successful response', function (): void {
    actingAsSuperAdmin();

    $this->get('/admin/other/profile')->assertStatus(200);
});

it('loads the current user data on mount', function (): void {
    $user = actingAsSuperAdmin();
    $user->name = 'Jana Nováková';
    $user->badge_color = BadgeColor::Teal;
    $user->saveOrFail();

    Livewire::test(UserProfileSettings::class)
        ->assertSet('name', 'Jana Nováková')
        ->assertSet('email', $user->email)
        ->assertSet('badge_color', BadgeColor::Teal->value);
});

it('hydrates the avatar field as an array so a new upload can attach to it', function (): void {
    // FileUpload wraps its raw value into an array via its own afterStateHydrated() hook,
    // which only runs when hydration goes through the schema's fill(). mount() must use
    // fill() rather than assigning $this->avatar directly, or the browser's upload-finish
    // callback fails trying to set a nested "avatar.<key>" path on a null/string property
    // (Livewire: "Property type not supported ... for property: [null]").
    Storage::fake('public');

    $user = actingAsSuperAdmin();
    $path = "avatars/{$user->id}/existing.jpg";
    Storage::disk('public')->put($path, 'fake-image-content');
    $user->avatar_path = $path;
    $user->saveOrFail();

    $withAvatar = Livewire::test(UserProfileSettings::class)->get('avatar');
    expect($withAvatar)->toBeArray()->and($withAvatar)->toContain($path);

    $user->avatar_path = null;
    $user->saveOrFail();

    $withoutAvatar = Livewire::test(UserProfileSettings::class)->get('avatar');
    expect($withoutAvatar)->toBe([]);
});

it('updates the name and badge color', function (): void {
    $user = actingAsSuperAdmin();

    Livewire::test(UserProfileSettings::class)
        ->set('name', 'Petr Svoboda')
        ->set('badge_color', BadgeColor::Rose->value)
        ->call('submit')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->name)->toBe('Petr Svoboda');
    expect($user->badge_color)->toBe(BadgeColor::Rose);
});

it('does not let the email be changed', function (): void {
    $user = actingAsSuperAdmin();
    $originalEmail = $user->email;

    Livewire::test(UserProfileSettings::class)
        ->set('email', 'someone-else@example.com')
        ->set('name', $user->name)
        ->call('submit')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->email)->toBe($originalEmail);
});

it('removes the stored avatar file when the avatar is cleared', function (): void {
    Storage::fake('public');

    $user = actingAsSuperAdmin();
    $path = "avatars/{$user->id}/old.jpg";
    Storage::disk('public')->put($path, 'fake-image-content');
    $user->avatar_path = $path;
    $user->saveOrFail();

    Livewire::test(UserProfileSettings::class)
        ->set('avatar', null)
        ->set('name', $user->name)
        ->call('submit')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->avatar_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('ignores a client-supplied avatar path outside the user own upload directory', function (): void {
    // The FileUpload field has no statePath binding, so its value is a plain, client-writable
    // Livewire property: a crafted request (bypassing the upload widget entirely) could set it
    // to any string, e.g. another user's real avatar path. resolveAvatarPath() is the guard
    // against that, checked directly here since simulating the raw wire payload shape is an
    // internal Filament implementation detail, not something worth coupling this test to.
    Storage::fake('public');

    $user = actingAsSuperAdmin();
    Storage::disk('public')->put('avatars/999/someone-elses-avatar.jpg', 'fake-image-content');

    $page = new UserProfileSettings();

    $reflection = new ReflectionMethod($page, 'resolveAvatarPath');
    $reflection->setAccessible(true);

    expect($reflection->invoke($page, $user, 'avatars/999/someone-elses-avatar.jpg'))->toBeNull();
});

it('accepts an avatar path inside the user own upload directory', function (): void {
    Storage::fake('public');

    $user = actingAsSuperAdmin();
    $path = "avatars/{$user->id}/new.jpg";
    Storage::disk('public')->put($path, 'fake-image-content');

    $page = new UserProfileSettings();

    $reflection = new ReflectionMethod($page, 'resolveAvatarPath');
    $reflection->setAccessible(true);

    expect($reflection->invoke($page, $user, $path))->toBe($path);
});

it('keeps the avatar when resaving other fields without touching it', function (): void {
    // Regression: getState() applies FileUpload's state cast (array -> single value) only to
    // the array IT RETURNS, never in place on $this->avatar. Reading $this->avatar directly
    // after calling getState() silently wiped the avatar on every save.
    Storage::fake('public');

    $user = actingAsSuperAdmin();
    $path = "avatars/{$user->id}/existing.jpg";
    Storage::disk('public')->put($path, 'fake-image-content');
    $user->avatar_path = $path;
    $user->saveOrFail();

    Livewire::test(UserProfileSettings::class)
        ->set('name', $user->name)
        ->call('submit')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->avatar_path)->toBe($path);
    Storage::disk('public')->assertExists($path);
});

it('requires a name', function (): void {
    actingAsSuperAdmin();

    Livewire::test(UserProfileSettings::class)
        ->set('name', '')
        ->call('submit')
        ->assertHasErrors(['name' => 'required']);
});
