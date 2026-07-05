<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SportEventLinkType;
use App\Models\SportEvent;
use App\Models\SportEventLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportEventLink>
 */
class SportEventLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sport_event_id' => SportEvent::factory(),
            'external_key'   => null,
            'internal'       => false,
            'source_path'    => null,
            'source_url'     => $this->faker->url(),
            'source_type'    => SportEventLinkType::Invitation,
            'name_cz'        => $this->faker->sentence(2),
            'name_en'        => null,
            'description_cz' => $this->faker->optional()->sentence(),
            'description_en' => null,
        ];
    }

    public function ofType(SportEventLinkType $type): static
    {
        return $this->state(fn (): array => [
            'source_type' => $type,
        ]);
    }
}
