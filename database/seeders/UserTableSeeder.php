<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
        $envPassword = env('ADMIN_USER_PASSWORD', 'secret');

        DB::table('users')->insert([
            'name' => env('ADMIN_USER_NAME', 'Admin'),
            'email' => env('ADMIN_USER_EMAIL', 'admin@example.com'),
            'password' => bcrypt(is_string($envPassword) ? $envPassword : 'secret'),
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);
    }
}
