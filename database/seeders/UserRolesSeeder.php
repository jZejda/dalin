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
        Role::create([
            'name'  => 'redactor',
            'guard_name'  => 'web',
        ]);

        Role::create([
            'name'  => 'event_master',
            'guard_name'  => 'web',
        ]);

        Role::create([
            'name'  => 'member',
            'guard_name'  => 'web',
        ]);

        Role::create([
            'name'  => 'billing_specialist',
            'guard_name'  => 'web',
        ]);
    }
}
