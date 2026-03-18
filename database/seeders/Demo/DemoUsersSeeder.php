<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        $roles = [
            'club_admin'          => 2,
            'event_master'        => 3,
            'billing_specialist'  => 2,
            'redactor'            => 3,
            'member'              => 15,
        ];

        foreach ($roles as $role => $count) {
            for ($i = 0; $i < $count; $i++) {
                $firstName = $faker->firstName();
                $lastName  = $faker->lastName();
                $email     = Str::lower(Str::ascii($firstName) . '.' . Str::ascii($lastName) . $faker->randomNumber(2)) . '@demo.cz';

                DB::table('users')->insert([
                    'name'                  => $firstName . ' ' . $lastName,
                    'email'                 => $email,
                    'password'              => bcrypt('Demo2026!'),
                    'active'                => true,
                    'payer_variable_symbol' => (string) $faker->unique()->randomNumber(8, true),
                    'email_verified_at'     => Carbon::now()->toDateTimeString(),
                    'created_at'            => Carbon::now()->subDays($faker->numberBetween(30, 730))->toDateTimeString(),
                    'updated_at'            => Carbon::now()->toDateTimeString(),
                ]);

                $user = User::where('email', $email)->first();
                $user?->assignRole($role);
            }
        }
    }
}
