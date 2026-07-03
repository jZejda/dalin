<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransportDirection;
use App\Models\TransportOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransportOffer>
 */
class TransportOfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'departure_place' => $this->faker->city(),
            'direction'       => TransportDirection::Both,
            'seats_offered'   => $this->faker->numberBetween(1, 4),
            'distance_km'     => $this->faker->numberBetween(10, 300),
            'contribution'    => $this->faker->optional()->randomFloat(2, 50, 500),
            'active'          => true,
        ];
    }
}
