<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissinsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date_create();

        DB::table('permissions')->insert([
            'name' => 'Admin rolePerm',
            'guard_name' => 'web',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
        DB::table('permissions')->insert([
            'name' => 'Create Post',
            'guard_name' => 'web',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Post',
            'guard_name' => 'web',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Post',
            'guard_name' => 'web',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);

        DB::table('permissions')->insert([
            'name' => 'Create Page',
            'guard_name' => 'web',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
        DB::table('permissions')->insert([
            'name' => 'Edit Page',
            'guard_name' => 'web',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
        DB::table('permissions')->insert([
            'name' => 'Delete Page',
            'guard_name' => 'web',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
    }
}
