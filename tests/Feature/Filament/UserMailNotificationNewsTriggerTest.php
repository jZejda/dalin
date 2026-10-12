<?php

declare(strict_types=1);

use App\Filament\Clusters\Other\Pages\UserMailNotification;
use App\Models\UserSetting;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = actingAsSuperAdmin();
});

test('news digest hour field is rendered on the settings page', function () {
    Livewire::test(UserMailNotification::class)
        ->assertSee(__('user-mail-notification.form.news_time_trigger'))
        ->assertSet('news_time_trigger', 17);
});

test('stored news digest hour is loaded into the form', function () {
    UserSetting::query()->create([
        'user_id' => $this->user->id,
        'type' => UserSetting::TYPE_MAIL,
        'options' => ['news' => ['0'], 'news_time_trigger' => 8],
    ]);

    Livewire::test(UserMailNotification::class)
        ->assertSet('news_time_trigger', 8);
});

test('news digest hour is saved with the mail settings', function () {
    Livewire::test(UserMailNotification::class)
        ->set('news', ['0', '1'])
        ->set('news_time_trigger', 6)
        ->call('submit')
        ->assertHasNoErrors();

    $setting = UserSetting::query()
        ->where('user_id', '=', $this->user->id)
        ->where('type', '=', UserSetting::TYPE_MAIL)
        ->firstOrFail();

    expect($setting->options['news'])->toBe(['0', '1'])
        ->and($setting->options['news_time_trigger'])->toBe(6);
});

test('news digest hour above 23 is rejected', function () {
    Livewire::test(UserMailNotification::class)
        ->set('news_time_trigger', 24)
        ->call('submit')
        ->assertHasErrors(['news_time_trigger']);
});
