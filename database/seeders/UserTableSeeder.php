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

        DB::table('users')->insert([
            'name' => env('ADMIN_USER_NAME', 'Admin'),
            'email' => env('ADMIN_USER_EMAIL', 'admin@example.com'),
            'password' => bcrypt(env('ADMIN_USER_PASSWORD', 'secret')),
            'created_at' => Carbon::now()->toDayDateTimeString(),
            'updated_at' => Carbon::now()->toDayDateTimeString(),
        ]);

        //TODO delete
        //        DB::table('users')->insert([
        //            'name' => 'Virtualr',
        //            'email' => 'virtual@example.com',
        //            'password' => bcrypt(env('VIRTUAL_USER_PASSWORD', 'secret')),
        //            'created_at' => date_format($date, 'Y-m-d H:i:s'),
        //            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        //        ]);
    }
}
