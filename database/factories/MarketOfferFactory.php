<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MarketOfferStatus;
use App\Models\MarketOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketOffer>
 */
class MarketOfferFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'is_club_offer' => false,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => MarketOfferStatus::Active,
            'closes_at' => now()->addDays(7),
            'closed_at' => null,
        ];
    }

    public function clubOffer(): static
    {
        return $this->state(fn (): array => [
            'is_club_offer' => true,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (): array => [
            'status' => MarketOfferStatus::Closed,
            'closes_at' => now()->subDay(),
            'closed_at' => now()->subDay(),
        ]);
    }
}
