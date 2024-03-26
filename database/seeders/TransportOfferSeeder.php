<?php

namespace Database\Seeders;

use App\Models\TransportOffer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransportOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransportOffer::factory()->count(20)->create();
    }
}
