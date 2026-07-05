<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'user_id'             => User::factory(),
            'content_category_id' => null,
            'title'               => $title,
            'slug'                => Str::slug($title),
            'content'             => '<p>' . $this->faker->paragraph() . '</p><p>' . $this->faker->paragraph() . '</p>',
            'content_format'      => ContentFormat::Html,
            'picture_attachment'  => '',
            'status'              => PageStatus::Open,
            'weight'              => $this->faker->numberBetween(10, 90),
            'page_menu'           => false,
            'meta'                => null,
        ];
    }

    public function inMenu(): static
    {
        return $this->state(fn (): array => [
            'page_menu' => true,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => PageStatus::Draft,
        ]);
    }
}
