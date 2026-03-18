<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\User;
use App\Models\UserRaceProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoUserRaceProfileSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        // All non-admin users get a race profile
        $users = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super_admin');
        })->get();

        $licences = ['A', 'B', 'C', 'D', 'E', null];
        $genders  = ['M', 'F'];

        foreach ($users as $user) {
            $firstName = explode(' ', $user->name)[0] ?? $faker->firstName();
            $lastName  = explode(' ', $user->name)[1] ?? $faker->lastName();
            $gender    = $faker->randomElement($genders);

            UserRaceProfile::create([
                'user_id'      => $user->id,
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'reg_number'   => 'ABC' . $faker->unique()->randomNumber(4, true),
                'gender'       => $gender,
                'email'        => $user->email,
                'phone'        => $faker->phoneNumber(),
                'city'         => $faker->city(),
                'street'       => $faker->streetAddress(),
                'zip'          => $faker->postcode(),
                'si'           => $faker->optional(0.8)->numberBetween(100000, 9999999),
                'licence_ob'   => $faker->randomElement($licences),
                'licence_lob'  => $faker->optional(0.3)->randomElement(['A', 'B', 'C']),
                'licence_mtbo' => $faker->optional(0.2)->randomElement(['A', 'B', 'C']),
                'active_until' => Carbon::now()->addYear()->toDateString(),
                'active'       => true,
            ]);
        }
    }
}
