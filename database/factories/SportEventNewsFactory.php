<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SportEventNews;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportEventNews>
 */
class SportEventNewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text' => fake()->text(),
            'date' => fake()->date(),
        ];
    }
}
