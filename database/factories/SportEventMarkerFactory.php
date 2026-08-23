<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SportEventMarkerType;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportEventMarker>
 */
class SportEventMarkerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sport_event_id' => SportEvent::factory(),
            'external_key'   => null,
            'letter'         => $this->faker->randomLetter(),
            'label'          => $this->faker->streetName(),
            'desc'           => $this->faker->optional()->sentence(),
            'lat'            => $this->faker->randomFloat(6, 48.55, 51.05),
            'lon'            => $this->faker->randomFloat(6, 12.09, 18.86),
            'type'           => SportEventMarkerType::DefaultMarker,
        ];
    }

    public function ofType(SportEventMarkerType $type): static
    {
        return $this->state(fn (): array => [
            'type' => $type,
        ]);
    }
}
