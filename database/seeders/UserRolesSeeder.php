<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $roles = ['redactor', 'event_master', 'member', 'billing_specialist', 'event_organizer'];

        foreach ($roles as $role) {
            Role::create([
                'name'  => $role,
                'guard_name'  => 'web',
            ]);
        }
    }
}
