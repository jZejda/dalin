<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SportDiscipline;
use Illuminate\Database\Seeder;

class SportDisciplinesSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::getSeederData() as $orisId => $data) {
            $discipline = SportDiscipline::query()->find($orisId) ?? new SportDiscipline();
            $discipline->id = $orisId;
            $discipline->fill($data);
            $discipline->save();
        }
    }

    /**
     * Číselník disciplín dle ORIS (Discipline), id odpovídá ORIS ID. ID 8 v číselníku ORIS chybí.
     *
     * @return array<int, array{short_name: string, long_name: string, relays: bool}>
     */
    public static function getSeederData(): array
    {
        return [
            1 => ['short_name' => 'LD', 'long_name' => 'Dlouhá trať', 'relays' => false],
            2 => ['short_name' => 'MD', 'long_name' => 'Střední trať', 'relays' => false],
            3 => ['short_name' => 'SP', 'long_name' => 'Sprint', 'relays' => false],
            4 => ['short_name' => 'UD', 'long_name' => 'Ultralong trať', 'relays' => false],
            5 => ['short_name' => 'RE', 'long_name' => 'Štafety', 'relays' => true],
            6 => ['short_name' => 'TE', 'long_name' => 'Družstva', 'relays' => true],
            7 => ['short_name' => 'FO', 'long_name' => 'Volné pořadí kontrol', 'relays' => false],
            9 => ['short_name' => 'NO', 'long_name' => 'Noční', 'relays' => false],
            10 => ['short_name' => 'Z', 'long_name' => 'Dlouhodobé žebříčky', 'relays' => false],
            11 => ['short_name' => 'TO', 'long_name' => 'TempO', 'relays' => false],
            12 => ['short_name' => 'SEM', 'long_name' => 'Školení, schůze, semináře', 'relays' => false],
            13 => ['short_name' => 'ET', 'long_name' => 'Etapový závod', 'relays' => false],
            14 => ['short_name' => 'MS', 'long_name' => 'Hromadný start', 'relays' => false],
            15 => ['short_name' => 'SR', 'long_name' => 'Sprintové štafety', 'relays' => true],
            16 => ['short_name' => 'KO', 'long_name' => 'Knock-out sprint', 'relays' => false],
            17 => ['short_name' => 'STK', 'long_name' => 'Stacionární tréninkový kemp', 'relays' => false],
            18 => ['short_name' => 'AT', 'long_name' => 'Atletika', 'relays' => false],
            19 => ['short_name' => 'IN', 'long_name' => 'Indoor', 'relays' => false],
            20 => ['short_name' => 'TC', 'long_name' => 'Soustředění', 'relays' => false],
            21 => ['short_name' => 'REPRE', 'long_name' => 'Reprezentace OB', 'relays' => false],
        ];
    }
}
