<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\TransportDirection;
use App\Enums\VehicleType;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoTransportSeeder extends Seeder
{
    public function run(): void
    {
        $faker   = \Faker\Factory::create('cs_CZ');
        $members = User::role('member')->get();

        if ($members->count() < 5) {
            return;
        }

        /** @var \Illuminate\Support\Collection<int, User> $drivers */
        $drivers = $members->random(4);

        // Vehicles owned by demo members
        $vehicles = collect();

        foreach ($drivers as $driver) {
            $vehicles->push(Vehicle::factory()->ownedBy($driver)->default()->create([
                'name'  => $faker->randomElement(['Rodinné kombi', 'Služební auto', 'Osobní auto', 'Karavan']),
                'type'  => VehicleType::PassengerCar->value,
                'seats' => $faker->numberBetween(4, 5),
            ]));
        }

        // Club van owned by the first driver
        $firstDriver = $drivers->first();

        if ($firstDriver !== null) {
            $vehicles->push(Vehicle::factory()->ownedBy($firstDriver)->ofType(VehicleType::Van)->create([
                'name'     => 'Klubová dodávka',
                'brand'    => 'Ford',
                'seats'    => 9,
                'operator' => 'SK Demo Orientace',
            ]));
        }

        // Transport offers on upcoming events with requests from other members
        $futureEvents = SportEvent::where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->take(5)
            ->get();

        foreach ($futureEvents as $event) {
            /** @var Vehicle $vehicle */
            $vehicle = $vehicles->random();

            $offer = TransportOffer::factory()->create([
                'sport_event_id'  => $event->id,
                'user_id'         => $vehicle->user_id,
                'vehicle_id'      => $vehicle->id,
                'departure_place' => $faker->randomElement(['Brno, Riviéra', 'Parkoviště u sokolovny', 'Praha, Černý Most', 'U klubovny']),
                'direction'       => TransportDirection::Both,
                'seats_offered'   => max(1, $vehicle->seats - 1),
                'distance_km'     => $faker->numberBetween(20, 250),
                'contribution'    => $faker->randomElement([null, 100.0, 150.0, 200.0]),
            ]);

            $passengers = $members->where('id', '!=', $vehicle->user_id)->random(2);

            foreach ($passengers->values() as $index => $passenger) {
                $factory = TransportRequest::factory();

                if ($index === 0) {
                    $factory = $factory->approved();
                }

                $factory->create([
                    'transport_offer_id' => $offer->id,
                    'user_id'            => $passenger->id,
                    'direction'          => $faker->randomElement([TransportDirection::Both, TransportDirection::There]),
                    'seats'              => $faker->numberBetween(1, 2),
                ]);
            }
        }
    }
}
