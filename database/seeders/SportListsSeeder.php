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
        ]);

        SportList::create([
            'short_name'  => 'LOB',
        ]);

        SportList::create([
            'short_name'  => 'MTBO',
        ]);

        SportList::create([
            'short_name'  => 'TRAIL',
        ]);
    }
}
