<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SportDiscipline;
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
            'short_name'  => 'KL',
            'long_name'  => 'Klasická trať',
        ]);

        SportDiscipline::create([
            'short_name'  => 'KT',
            'long_name'  => 'Krátká trať',
        ]);

        SportDiscipline::create([
            'short_name'  => 'SP',
            'long_name'  => 'Sprint',
        ]);

        SportDiscipline::create([
            'short_name'  => 'DT',
            'long_name'  => 'Dlouhá trať',
        ]);

        SportDiscipline::create([
            'short_name'  => 'ST',
            'long_name'  => 'Štafety',
        ]);

        SportDiscipline::create([
            'short_name'  => 'DR',
            'long_name'  => 'Družstva',
        ]);

        SportDiscipline::create([
            'short_name'  => 'SC',
            'long_name'  => 'Volné pořadí kontrol - scorelauf',
        ]);

        SportDiscipline::create([
            'short_name'  => 'NOB',
            'long_name'  => 'Noční',
        ]);

        SportDiscipline::create([
            'short_name'  => 'Z',
            'long_name'  => 'Dlouhoběhé závody',
        ]);

        SportDiscipline::create([
            'short_name'  => 'TeO',
            'long_name'  => 'TempO',
        ]);


        SportDiscipline::create([
            'short_name'  => 'S',
            'long_name'  => 'Školení, Schůze, semináře',
        ]);

        SportDiscipline::create([
            'short_name'  => 'ET',
            'long_name'  => 'Etapový',
        ]);

        SportDiscipline::create([
            'short_name'  => 'MS',
            'long_name'  => 'Hromadný start',
        ]);

        SportDiscipline::create([
            'short_name'  => 'SS',
            'long_name'  => 'Sprintové štafety',
        ]);

        SportDiscipline::create([
            'short_name'  => 'KO',
            'long_name'  => 'Knock-out sprint',
        ]);

    }
}
