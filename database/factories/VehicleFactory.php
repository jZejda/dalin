<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'      => null,
            'name'         => $this->faker->randomElement(['Klubový bus', 'Dodávka', 'Osobní auto']).' '.$this->faker->numberBetween(1, 99),
            'brand'        => $this->faker->randomElement(['Škoda', 'Volkswagen', 'Ford', 'Mercedes-Benz']),
            'description'  => $this->faker->optional()->sentence(),
            'seats'        => $this->faker->numberBetween(4, 50),
            'operator'     => $this->faker->optional()->company(),
            'consumption'  => $this->faker->randomFloat(2, 5, 25),
            'price_per_km' => $this->faker->randomFloat(2, 3, 15),
            'active'       => true,
        ];
    }

    public function ownedBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'active' => false,
        ]);
    }
}
