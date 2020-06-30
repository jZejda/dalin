<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        // Posts premissions
        Permission::create(['name' => 'Admin RolePerm']);
        Permission::create(['name' => 'Create Post']);
        Permission::create(['name' => 'Edit Post']);
        Permission::create(['name' => 'Delete Post']);

        // Pages permissions
        Permission::create(['name' => 'Create Page']);
        Permission::create(['name' => 'Edit Page']);
        Permission::create(['name' => 'Delete Page']);

        // Media permissions
        Permission::create(['name' => 'Add File']);
        Permission::create(['name' => 'Delete File']);

        // Members permissions
        Permission::create(['name' => 'Edit member']);
        Permission::create(['name' => 'Edit member password']);

        // Roles and Permissions permissions
        Permission::create(['name' => 'Manage roles']);
        Permission::create(['name' => 'Manage permissions']);

        // Events Permissions
        Permission::create(['name' => 'Show Oevents']);
        Permission::create(['name' => 'Create Oevents']);
        Permission::create(['name' => 'Edit Oevents']);
        Permission::create(['name' => 'Delete Oevents']);

        Permission::create(['name' => 'Show Legs']);
        Permission::create(['name' => 'Create Legs']);
        Permission::create(['name' => 'Edit Legs']);
        Permission::create(['name' => 'Delete Legs']);


        // Roles
        $role = Role::create(['name' => 'Super Admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'Moderator']);
        $role->givePermissionTo(['Create Post',
                                'Edit Post',
                                'Delete Post',
                                'Create Page',
                                'Edit Page',
                                'Delete Page',
                                'Add File',
                                'Delete File',
                              ]);

        $role = Role::create(['name' => 'Event Manager']);
        $role->givePermissionTo(['Show Oevents',
                                 'Create Oevents',
                                 'Edit Oevents',
                                 'Delete Oevents',
                                 'Show Legs',
                                 'Create Legs',
                                 'Edit Legs',
                                 'Delete Legs']);

        $role = Role::create(['name' => 'Member']);
        $role->givePermissionTo(['Edit member']);
        $role->givePermissionTo(['Edit member password']);

        // Add role to Admin


        DB::table('model_has_roles')->insert([
            'role_id'     => 1,
            'model_type'  => \App\User::class,
            'model_id'    => 1,
        ]);

    }
}
