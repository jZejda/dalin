<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Resources\Users\Pages\ListUsers;
use App\Mail\UserPasswordSend;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

use function Pest\Livewire\livewire;

/**
 * @param  array<int, string>  $permissions
 */
function actingAsUserManager(array $permissions): User
{
    $user = User::factory()->create();

    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission, 'web');
    }

    $user->givePermissionTo($permissions);
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    test()->actingAs($user);

    return $user;
}

it('hides the account row actions from a user who may only list users', function () {
    actingAsUserManager(['ViewAny:User']);

    $target = User::factory()->create(['active' => true]);

    livewire(ListUsers::class)
        ->assertTableActionHidden('reset_password', $target)
        ->assertTableActionHidden('change_status', $target);
});

it('does not run the account row actions for a user who may only list users', function () {
    Mail::fake();
    actingAsUserManager(['ViewAny:User']);

    $target = User::factory()->create(['active' => true]);
    $originalHash = $target->password;

    // Call the Livewire endpoints directly, the way a crafted request would, instead of going through the UI helpers
    foreach (['reset_password', 'change_status'] as $action) {
        livewire(ListUsers::class)
            ->call('mountAction', $action, ['active' => 0], ['table' => true, 'recordKey' => (string) $target->getKey()])
            ->call('callMountedAction', ['active' => 0]);
    }

    $target->refresh();

    expect($target->password)->toBe($originalHash)
        ->and($target->active)->toBeTrue();
    Mail::assertNothingQueued();
});

it('resets the password with a generated value and mails it to the user', function () {
    Mail::fake();
    actingAsUserManager(['ViewAny:User', 'Update:User']);

    $target = User::factory()->create();
    $originalHash = $target->password;

    livewire(ListUsers::class)
        ->call('mountAction', 'reset_password', ['password' => 'chosen-by-the-caller'], ['table' => true, 'recordKey' => (string) $target->getKey()])
        ->call('callMountedAction', ['password' => 'chosen-by-the-caller']);

    $target->refresh();

    expect($target->password)->not->toBe($originalHash)
        ->and(Hash::check('chosen-by-the-caller', $target->password))->toBeFalse();
    Mail::assertQueued(UserPasswordSend::class, fn (UserPasswordSend $mail): bool => $mail->hasTo($target->email));
});

it('changes the account status for a user who may update users', function () {
    actingAsUserManager(['ViewAny:User', 'Update:User']);

    $target = User::factory()->create(['active' => true]);

    livewire(ListUsers::class)
        ->callTableAction('change_status', $target, data: ['active' => 0])
        ->assertHasNoTableActionErrors();

    expect($target->refresh()->active)->toBeFalse();
});

it('keeps super admin accounts manageable by super admins only', function () {
    actingAsUserManager(['ViewAny:User', 'Update:User']);

    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $admin = User::factory()->create(['active' => true]);
    $admin->assignRole('super_admin');

    livewire(ListUsers::class)
        ->assertTableActionHidden('reset_password', $admin)
        ->assertTableActionHidden('change_status', $admin);

    actingAsSuperAdmin();

    livewire(ListUsers::class)
        ->assertTableActionVisible('reset_password', $admin)
        ->assertTableActionVisible('change_status', $admin);
});

it('shows the user name as text in the row action modals', function () {
    actingAsSuperAdmin();

    $target = User::factory()->create(['name' => 'Jan <img src=x onerror=alert(1)> Novák']);

    foreach (['reset_password', 'change_status'] as $action) {
        livewire(ListUsers::class)
            ->mountTableAction($action, $target)
            ->assertDontSeeHtml('<img src=x onerror=alert(1)>')
            ->assertSeeHtml('Jan &lt;img src=x onerror=alert(1)&gt; Novák');
    }
});
