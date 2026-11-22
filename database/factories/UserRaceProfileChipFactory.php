<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SiChipSport;
use App\Enums\SiChipType;
use App\Models\UserRaceProfile;
use App\Models\UserRaceProfileChip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserRaceProfileChip>
 */
class UserRaceProfileChipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_race_profile_id' => UserRaceProfile::factory(),
            'si' => $this->faker->unique()->numberBetween(100000, 9999999),
            'sport' => SiChipSport::All,
            'type' => SiChipType::All,
            'sort_order' => 1,
        ];
    }

    public function forSport(SiChipSport $sport): static
    {
        return $this->state(fn (): array => ['sport' => $sport]);
    }
}
