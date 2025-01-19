<?php

namespace Database\Seeders;

use App\Models\SportLevel;
use Illuminate\Database\Seeder;

class SportLevelSeeder extends Seeder
{
    public function run(): void
    {
        SportLevel::create([
            'oris_id'  => 1,
            'short_name'  => 'MČR',
            'long_name'  => 'Mistrovství ČR',
        ]);

        SportLevel::create([
            'oris_id'  => 2,
            'short_name'  => 'ŽA',
            'long_name'  => 'Žebříček A',
        ]);

        SportLevel::create([
            'oris_id'  => 3,
            'short_name'  => 'ŽB',
            'long_name'  => 'Žebříček B',
        ]);

        SportLevel::create([
            'oris_id'  => 4,
            'short_name'  => 'OŽ',
            'long_name'  => 'Oblastní žebříček',
        ]);

        SportLevel::create([
            'oris_id'  => 5,
            'short_name'  => 'E',
            'long_name'  => 'Jednotlivá etapa',
        ]);

        SportLevel::create([
            'oris_id'  => 6,
            'short_name'  => 'OST',
            'long_name'  => 'Ostatní',
        ]);

        SportLevel::create([
            'oris_id'  => 7,
            'short_name'  => 'ČPŠ',
            'long_name'  => 'Český pohár štafet',
        ]);

        SportLevel::create([
            'oris_id'  => 8,
            'short_name'  => 'ČP',
            'long_name'  => 'Český pohár',
        ]);

        SportLevel::create([
            'oris_id'  => 9,
            'short_name'  => 'ČLK',
            'long_name'  => 'Česká liga klubů',
        ]);
        SportLevel::create([
            'oris_id'  => 10,
            'short_name'  => 'SC',
            'long_name'  => 'Sprint cup',
        ]);
        SportLevel::create([
            'oris_id'  => 11,
            'short_name'  => 'OM',
            'long_name'  => 'Oblastní mistrovství ČR',
        ]);
        SportLevel::create([
            'oris_id'  => 12,
            'short_name'  => 'WRE',
            'long_name'  => 'World ranking',
        ]);
        SportLevel::create([
            'oris_id'  => 13,
            'short_name'  => 'ZL',
            'long_name'  => 'Zimní liga',
        ]);
        SportLevel::create([
            'oris_id'  => 14,
            'short_name'  => 'OF',
            'long_name'  => 'Ostatní oficiální',
        ]);
        SportLevel::create([
            'oris_id'  => 15,
            'short_name'  => 'S',
            'long_name'  => 'Školení, schůze, semináře',
        ]);
        SportLevel::create([
            'oris_id'  => 16,
            'short_name'  => 'ET',
            'long_name'  => 'Etapový závod',
        ]);
        SportLevel::create([
            'oris_id'  => 10,
            'short_name'  => 'VET',
            'long_name'  => 'Veteraniáda ČR',
        ]);
        SportLevel::create([
            'oris_id'  => 18,
            'short_name'  => 'R',
            'long_name'  => 'Regionální závod',
        ]);
    }
}
