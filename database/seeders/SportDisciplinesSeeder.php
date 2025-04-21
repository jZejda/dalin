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
        foreach (self::getSeederData() as $rowKey => $rowValue) {
            SportDiscipline::query()->create([
                'short_name'  => $rowKey,
                'long_name'  => $rowValue,
            ]);
        }
    }

    public static function getSeederData(): array
    {
        return [
            'KL' => 'Klasická trať',
            'KT' => 'Krátká trať',
            'SP' => 'Sprint',
            'DT' => 'Dlouhá trať',
            'ST' => 'Štafety',
            'DR' => 'Družstva',
            'SC' => 'Volné pořadí kontrol - scorelauf',
            'NOB' => 'Noční',
            'Z' => 'Dlouhoběhé závody',
            'TeO' => 'TempO',
            'S' => 'Školení, Schůze, semináře',
            'ET' => 'Etapový',
            'MS' => 'Hromadný start',
            'SS' => 'Sprintové štafety',
            'KO' => 'Knock-out sprint',
        ];
    }
}
