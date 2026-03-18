<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'super_admin')->where('guard_name', 'web')->firstOrFail();
        $permissions = Permission::where('guard_name', 'web')->get();

        $role->syncPermissions($permissions);

        /** @phpstan-ignore larastan.noEnvCallsOutsideOfConfig */
        $adminEmail = env('ADMIN_USER_EMAIL', 'admin@example.com');
        $admin = User::where('email', $adminEmail)->first();
        $admin?->assignRole($role);
    }
}
