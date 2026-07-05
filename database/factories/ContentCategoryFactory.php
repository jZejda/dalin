<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ContentCategory>
 */
class ContentCategoryFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->word() . ' ' . $this->faker->word();

        return [
            'title'          => Str::ucfirst($title),
            'description'    => $this->faker->sentence(),
            'slug'           => Str::slug($title),
            'sport_event_id' => null,
        ];
    }
}
