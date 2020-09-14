<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppotherTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = date_create();

        DB::table('content_categories')->insert([
            'title' => 'Nezařazeno',
            'description' => 'Stránky nezařazeny do žádné specifické kategorie',
            'slug' => 'bezkategorie',
            'created_at' => date_format($date, 'Y-m-d H:i:s'),
            'updated_at' => date_format($date, 'Y-m-d H:i:s'),
        ]);
    }
}
