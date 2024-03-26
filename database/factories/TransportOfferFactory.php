<?php

namespace Database\Factories;

use App\Enums\TransportOfferDirection;
use App\Models\TransportOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransportOffer>
 */
class TransportOfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'direction'  => $this->faker->randomElement(TransportOfferDirection::cases())->value,
            'free_seats' => $this->faker->randomNumber(1),
            'distance' => $this->faker->randomNumber(2),
            'transport_contribution' => $this->faker->randomFloat(1, 0, 0.9),
            'description' => $this->faker->realText()
        ];
    }
}
