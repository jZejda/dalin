<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Discipline;
use App\Models\Region;
use App\Models\Sport;

class ServiceEventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //----------------------------------------------------------------------------------------------------Discipline
        Discipline::create([
            'short_name'  => 'KLA',
            'long_name'  => 'Klasická trať',
        ]);

        Discipline::create([
            'short_name'  => 'KTR',
            'long_name'  => 'Krátká trať',
        ]);

        Discipline::create([
            'short_name'  => 'SPR',
            'long_name'  => 'Sprint',
        ]);

        Discipline::create([
            'short_name'  => 'DLT',
            'long_name'  => 'Dlouhá trať',
        ]);

        Discipline::create([
            'short_name'  => 'STA',
            'long_name'  => 'Štafety',
        ]);

        Discipline::create([
            'short_name'  => 'DRU',
            'long_name'  => 'Družstva',
        ]);

        Discipline::create([
            'short_name'  => 'SCO',
            'long_name'  => 'Volné pořadí kontrol - scorelauf',
        ]);

        Discipline::create([
            'short_name'  => 'NOB',
            'long_name'  => 'Noční',
        ]);

        Discipline::create([
            'short_name'  => 'DLZ',
            'long_name'  => 'Dlouhoběhé závody',
        ]);

        Discipline::create([
            'short_name'  => 'TeO',
            'long_name'  => 'TempO',
        ]);

        Discipline::create([
            'short_name'  => 'ETP',
            'long_name'  => 'Etapový',
        ]);

        Discipline::create([
            'short_name'  => 'MS',
            'long_name'  => 'Hromadný start',
        ]);

        Discipline::create([
            'short_name'  => 'OST',
            'long_name'  => 'Ostatní',
        ]);

        //------------------------------------------------------------------------------------------------------- Region
        Region::create([
            'short_name'  => 'ČR',
            'long_name'  => 'ČR',
        ]);

        Region::create([
            'short_name'  => 'ČE',
            'long_name'  => 'Čechy',
        ]);

        Region::create([
            'short_name'  => 'MO',
            'long_name'  => 'Morava',
        ]);

        Region::create([
            'short_name'  => 'JM',
            'long_name'  => 'Jihomoravská',
        ]);

        Region::create([
            'short_name'  => 'HA',
            'long_name'  => 'Hanácká',
        ]);

        Region::create([
            'short_name'  => 'JČ',
            'long_name'  => 'Jihočeská',
        ]);

        Region::create([
            'short_name'  => 'JE',
            'long_name'  => 'Ještědská',
        ]);

        Region::create([
            'short_name'  => 'PR',
            'long_name'  => 'Pražská',
        ]);

        Region::create([
            'short_name'  => 'ST',
            'long_name'  => 'Středočeská',
        ]);

        Region::create([
            'short_name'  => 'VA',
            'long_name'  => 'Valašská',
        ]);

        Region::create([
            'short_name'  => 'VČ',
            'long_name'  => 'Východočeská',
        ]);

        Region::create([
            'short_name'  => 'VY',
            'long_name'  => 'Vysočina',
        ]);

        Region::create([
            'short_name'  => 'ZČ',
            'long_name'  => 'Západočeská',
        ]);

        //---------------------------------------------------------------------------------------------------------Sport

        Sport::create([
            'name'  => 'OB',
        ]);

        Sport::create([
            'name'  => 'LOB',
        ]);

        Sport::create([
            'name'  => 'MTBO',
        ]);

        Sport::create([
            'name'  => 'TRAIL',
        ]);

        //------------------------------------------------------------------------------------------------ Leg_link_type

        /*
        Oevent_link_type::create([
            'title' => 'Odkaz na přihlášky etapy',
            'description' => 'Odkaz na externí html stránku příhlášek do etapy.',
            'source' => 'url'
        ]);
        Oevent_link_type::create([
            'title' => 'Odkaz na výsledky etapy',
            'description' => 'Odkaz na externí html stránku výsledků etapy.',
            'source' => 'url'
        ]);
        Oevent_link_type::create([
            'title' => 'Odkaz na výsledky etapy s mezičasy',
            'description' => 'Odkaz na externí html stránku výsledků etapy s mezičasy.',
            'source' => 'url'
        ]);
        Oevent_link_type::create([
            'title' => 'Odkaz na startovku - kategorie',
            'description' => 'Odkaz na externí html stránku strártovky po kategoriích.',
            'source' => 'url'
        ]);
        Oevent_link_type::create([
            'title' => 'Odkaz na startovku - kluby',
            'description' => 'Odkaz na externí html stránku strártovky po klubech.',
            'source' => 'url'
        ]);
        Oevent_link_type::create([
            'title' => 'Startovka jedné etapy',
            'description' => 'Odkaz na interní soubor xml iofv3, zpracuje do formátovaného tvaru',
            'source' => 'iofv3_xml_file'
        ]);
        Oevent_link_type::create([
            'title' => 'Výsledký jedné etapy',
            'description' => 'Odkaz na interní soubor xml iofv3, zpracuje do formátovaného tvaru',
            'source' => 'iofv3_xml_file'
        ]);
        */
    }
}
