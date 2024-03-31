<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Shared\Helpers\AppHelper;
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
            'created_at' => Carbon::now()->format(AppHelper::MYSQL_DATE_TIME),
            'updated_at' => Carbon::now()->format(AppHelper::MYSQL_DATE_TIME),
        ]);

        DB::table('users')->insert([
            'name' => 'Member Common',
            'email' => 'virtual@example.com',
            'password' => bcrypt(env('VIRTUAL_USER_PASSWORD', 'secret')),
            'created_at' => Carbon::now()->format(AppHelper::MYSQL_DATE_TIME),
            'updated_at' => Carbon::now()->format(AppHelper::MYSQL_DATE_TIME),
        ]);
    }
}
