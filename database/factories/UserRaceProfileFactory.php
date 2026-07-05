<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserRaceProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<UserRaceProfile>
 */
class UserRaceProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'first_name'   => $this->faker->firstName(),
            'last_name'    => $this->faker->lastName(),
            'reg_number'   => 'ABC' . $this->faker->unique()->numberBetween(1000, 99999),
            'oris_id'      => null,
            'club_user_id' => null,
            'iof_id'       => null,
            'email'        => $this->faker->safeEmail(),
            'phone'        => $this->faker->phoneNumber(),
            'gender'       => $this->faker->randomElement(['M', 'F']),
            'street'       => $this->faker->streetAddress(),
            'city'         => $this->faker->city(),
            'zip'          => $this->faker->postcode(),
            'si'           => $this->faker->optional(0.8)->numberBetween(100000, 9999999),
            'licence_ob'   => $this->faker->randomElement(['A', 'B', 'C', null]),
            'licence_lob'  => null,
            'licence_mtbo' => null,
            'active_until' => Carbon::now()->addYear()->toDateString(),
            'active'       => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'active' => false,
        ]);
    }
}
