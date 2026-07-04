<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AppSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppSetting>
 */
class AppSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key'   => $this->faker->unique()->slug(2),
            'value' => $this->faker->boolean(),
        ];
    }
}
