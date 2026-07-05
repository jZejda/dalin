<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SportEventExportsType;
use App\Models\SportEventExport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SportEventExport>
 */
class SportEventExportFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'title'              => $title,
            'slug'               => Str::slug($title),
            'export_type'        => SportEventExportsType::EventEntryListCat,
            'start_time'         => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'sport_event_id'     => null,
            'sport_event_leg_id' => null,
            'file_type'          => SportEventExport::FILE_XML_IOF_V3,
            'result_path'        => 'exports/' . Str::slug($title) . '.xml',
        ];
    }
}
