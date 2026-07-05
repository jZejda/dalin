<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RelayTeam;
use App\Models\SportEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RelayTeam>
 */
class RelayTeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sport_event_id' => SportEvent::factory(),
            'sport_class_id' => null,
            'name'           => $this->faker->unique()->city() . ' ' . $this->faker->numberBetween(1, 9),
            'relay_type'     => 'ST',
            'slots_count'    => 3,
        ];
    }
}
