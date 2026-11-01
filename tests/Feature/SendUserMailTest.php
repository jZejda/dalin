<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Filament\Actions\HelpLinkAction;
use App\Filament\Pages\Actions\UserSendMail;
use App\Filament\Pages\Jobs\SendUserMail;
use App\Filament\Pages\UserSettings;
use App\Mail\UserAppNotification;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach ([AppRoles::Member, AppRoles::EventMaster] as $role) {
        Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
    }

    $this->member = User::factory()->create()->assignRole(AppRoles::Member->value);
    $this->eventMaster = User::factory()->create()->assignRole([AppRoles::Member->value, AppRoles::EventMaster->value]);
    $this->withoutRole = User::factory()->create();
    $this->inactiveMember = User::factory()->create(['active' => false])->assignRole(AppRoles::Member->value);
});

function recipientIds(array $targets): array
{
    return (new SendUserMail('Předmět', 'Obsah', null, $targets))->recipientsQuery()->pluck('id')->all();
}

it('selects every active user for the "all" target, including users without a role', function (): void {
    $ids = recipientIds([SendUserMail::TARGET_ALL]);

    expect($ids)
        ->toContain($this->member->id, $this->eventMaster->id, $this->withoutRole->id)
        ->not->toContain($this->inactiveMember->id)
        ->and(count($ids))->toBe(User::query()->where('active', 1)->count());
});

it('selects only active users with the chosen role', function (): void {
    expect(recipientIds([AppRoles::EventMaster->value]))
        ->toContain($this->eventMaster->id)
        ->not->toContain($this->member->id, $this->withoutRole->id, $this->inactiveMember->id);
});

it('mails a user with several selected roles only once', function (): void {
    $ids = recipientIds([AppRoles::Member->value, AppRoles::EventMaster->value]);

    expect(array_count_values($ids)[$this->eventMaster->id])->toBe(1)
        ->and($ids)->not->toContain($this->inactiveMember->id);
});

it('queues the mail to each recipient and returns their count', function (): void {
    Mail::fake();

    $count = (new SendUserMail('Předmět', 'Obsah', null, [AppRoles::EventMaster->value]))->send();

    expect($count)->toBe(User::query()->where('active', 1)->role(AppRoles::EventMaster->value)->count());
    Mail::assertQueued(UserAppNotification::class, fn (UserAppNotification $mail): bool => $mail->hasTo($this->eventMaster->email));
    Mail::assertNotQueued(UserAppNotification::class, fn (UserAppNotification $mail): bool => $mail->hasTo($this->inactiveMember->email));
});

it('builds a help link to a docs page anchor', function (): void {
    expect(HelpLinkAction::to('stranka-uzivatelska-nastaveni', 'komu-se-e-mail-pošle')->getUrl())
        ->toBe('https://docs.dalin.cz/napoveda/stranka-uzivatelska-nastaveni#komu-se-e-mail-po%C5%A1le')
        ->and(HelpLinkAction::to('stranka-finance')->getUrl())
        ->toBe('https://docs.dalin.cz/napoveda/stranka-finance');
});

it('sends the mail to all active users from the admin modal', function (): void {
    Mail::fake();
    actingAsSuperAdmin();

    Livewire::test(UserSettings::class)
        ->callAction('sendUserNotification', [
            'subject' => 'Klubová schůze',
            'replyTo' => 'predseda@example.com',
            'targetUsers' => [SendUserMail::TARGET_ALL],
            'content' => 'Zveme vás na klubovou schůzi.',
        ])
        ->assertHasNoActionErrors()
        ->assertNotified(__('filament/user-setting.actions.send_mail.notification_title'));

    Mail::assertQueued(UserAppNotification::class, fn (UserAppNotification $mail): bool => $mail->hasTo($this->withoutRole->email));
    Mail::assertQueuedCount(User::query()->where('active', 1)->count());
});

it('warns instead of reporting success when no active user matches', function (): void {
    Mail::fake();
    actingAsSuperAdmin();
    Role::firstOrCreate(['name' => AppRoles::Redactor->value, 'guard_name' => 'web']);
    // Only an inactive redactor exists (rolled back by DatabaseTransactions)
    User::role(AppRoles::Redactor->value)->update(['active' => false]);
    User::factory()->create(['active' => false])->assignRole(AppRoles::Redactor->value);

    Livewire::test(UserSettings::class)
        ->callAction('sendUserNotification', [
            'subject' => 'Redakční porada',
            'replyTo' => null,
            'targetUsers' => [AppRoles::Redactor->value],
            'content' => 'Porada redaktorů.',
        ])
        ->assertHasNoActionErrors()
        ->assertNotified(__('filament/user-setting.actions.send_mail.notification_empty_title'));

    Mail::assertNothingQueued();
});

it('lets only the sender roles send the e-mail', function (AppRoles $role, bool $canSend): void {
    Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);

    expect(UserSendMail::canSend(User::factory()->create()->assignRole($role->value)))->toBe($canSend);
})->with([
    'super admin' => [AppRoles::SuperAdmin, true],
    'event master' => [AppRoles::EventMaster, true],
    'billing specialist' => [AppRoles::BillingSpecialist, true],
    'event organizer' => [AppRoles::EventOrganizer, true],
    'redactor' => [AppRoles::Redactor, false],
    'member' => [AppRoles::Member, false],
]);

it('labels the target groups by the role names used across the app', function (): void {
    actingAsSuperAdmin();

    Livewire::test(UserSettings::class)
        ->mountAction('sendUserNotification')
        ->assertFormFieldExists('targetUsers', 'mountedActionSchema0', function (Select $field): bool {
            return $field->getOptions()[AppRoles::BillingSpecialist->value] === AppRoles::BillingSpecialist->getLabel()
                && $field->getOptions()[SendUserMail::TARGET_ALL] === __('filament/user-setting.actions.send_mail.target_users_all');
        });
});
