<?php

namespace Database\Seeders;

use App\Models\SportRegion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SportRegionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        SportRegion::create([
            'short_name'  => 'Č',
            'long_name'  => 'Čechy',
        ]);

        SportRegion::create([
            'short_name'  => 'ČR',
            'long_name'  => 'ČR',
        ]);

        SportRegion::create([
            'short_name'  => 'HA',
            'long_name'  => 'Hanácká',
        ]);

        SportRegion::create([
            'short_name'  => 'JČ',
            'long_name'  => 'Jihočeská',
        ]);

        SportRegion::create([
            'short_name'  => 'JE',
            'long_name'  => 'Ještědská',
        ]);

        SportRegion::create([
            'short_name'  => 'JM',
            'long_name'  => 'Jihomoravská',
        ]);

        SportRegion::create([
            'short_name'  => 'M',
            'long_name'  => 'Morava',
        ]);

        SportRegion::create([
            'short_name'  => 'MSK',
            'long_name'  => 'MS kraj',
        ]);

        SportRegion::create([
            'short_name'  => 'P',
            'long_name'  => 'Pražská',
        ]);

        SportRegion::create([
            'short_name'  => 'StČ',
            'long_name'  => 'Středočeská',
        ]);

        SportRegion::create([
            'short_name'  => 'VA',
            'long_name'  => 'Valašská',
        ]);

        SportRegion::create([
            'short_name'  => 'VČ',
            'long_name'  => 'Východočeská',
        ]);

        SportRegion::create([
            'short_name'  => 'VY',
            'long_name'  => 'Vysočina',
        ]);

        SportRegion::create([
            'short_name'  => 'ZČ',
            'long_name'  => 'Západočeská',
        ]);
    }
}
