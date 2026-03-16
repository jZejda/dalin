<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');
uses(Tests\TestCase::class)->in('Unit', 'Frontend');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/**
 * Create a user with the `super_admin` role, register a global Gate bypass for that role,
 * clear the Spatie permission cache, set the test to act as that user, and return the user.
 *
 * The created user is assigned the `super_admin` role; a Gate `before` callback is registered
 * to automatically authorize users with that role, and PermissionRegistrar::forgetCachedPermissions()
 * is invoked to clear cached permissions.
 *
 * @return User The created User instance with the `super_admin` role, made the current test actor.
 */

function actingAsSuperAdmin(): User
{
    Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole('super_admin');

    // Shield's define_via_gate is false, so register the bypass manually
    \Illuminate\Support\Facades\Gate::before(
        fn (object $u, string $ability): ?bool => $u->hasRole('super_admin') ? true : null
    );

    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    test()->actingAs($user);

    return $user;
}
