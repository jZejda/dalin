<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SportList;
use Illuminate\Database\Seeder;

class SportListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        SportList::create([
            'short_name'  => 'OB',
            // Odchylka od manual.ceskyorientak.cz/barvy (tam šedá) — vědomé rozhodnutí klubu, viz docs/map-icons.md
            'color'       => '#FF8C00',
        ]);

        SportList::create([
            'short_name'  => 'LOB',
            'color'       => '#1565C0',
        ]);

        SportList::create([
            'short_name'  => 'MTBO',
            'color'       => '#2E7D32',
        ]);

        SportList::create([
            'short_name'  => 'TRAIL',
            'color'       => '#6A1B9A',
        ]);
    }
}
