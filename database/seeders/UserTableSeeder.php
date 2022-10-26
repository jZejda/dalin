<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $date = date_create();

        DB::table('users')->insert([
            'name' => env('ADMIN_USER_NAME', 'Admin'),
            'email' => env('ADMIN_USER_EMAIL', 'admin@example.com'),
            'password' => bcrypt(env('ADMIN_USER_PASSWORD', 'secret')),
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
    }
}
