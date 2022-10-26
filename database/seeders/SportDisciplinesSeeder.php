<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SportDiscipline;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SportDisciplinesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        SportDiscipline::create([
            'short_name'  => 'KLA',
            'long_name'  => 'Klasická trať',
        ]);

        SportDiscipline::create([
            'short_name'  => 'KTR',
            'long_name'  => 'Krátká trať',
        ]);

        SportDiscipline::create([
            'short_name'  => 'SPR',
            'long_name'  => 'Sprint',
        ]);

        SportDiscipline::create([
            'short_name'  => 'DLT',
            'long_name'  => 'Dlouhá trať',
        ]);

        SportDiscipline::create([
            'short_name'  => 'STA',
            'long_name'  => 'Štafety',
        ]);

        SportDiscipline::create([
            'short_name'  => 'DRU',
            'long_name'  => 'Družstva',
        ]);

        SportDiscipline::create([
            'short_name'  => 'SCO',
            'long_name'  => 'Volné pořadí kontrol - scorelauf',
        ]);

        SportDiscipline::create([
            'short_name'  => 'NOB',
            'long_name'  => 'Noční',
        ]);

        SportDiscipline::create([
            'short_name'  => 'DLZ',
            'long_name'  => 'Dlouhoběhé závody',
        ]);

        SportDiscipline::create([
            'short_name'  => 'TeO',
            'long_name'  => 'TempO',
        ]);

        SportDiscipline::create([
            'short_name'  => 'ETP',
            'long_name'  => 'Etapový',
        ]);

        SportDiscipline::create([
            'short_name'  => 'MS',
            'long_name'  => 'Hromadný start',
        ]);

        SportDiscipline::create([
            'short_name'  => 'OST',
            'long_name'  => 'Ostatní',
        ]);

    }
}
