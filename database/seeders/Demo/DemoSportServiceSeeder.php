<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\ServiceOrderStatus;
use App\Models\SportEvent;
use App\Models\SportService;
use App\Models\SportServiceOrder;
use App\Models\SportServicePaymentDate;
use App\Models\User;
use App\Models\UserRaceProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSportServiceSeeder extends Seeder
{
    public function run(): void
    {
        $faker    = \Faker\Factory::create('cs_CZ');
        $profiles = UserRaceProfile::all();
        $admin    = User::where('email', 'admin@demo.cz')->first();

        if ($profiles->isEmpty() || $admin === null) {
            return;
        }

        $futureEvents = SportEvent::where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->take(4)
            ->get();

        $serviceTemplates = [
            ['name' => 'Ubytování – tělocvična (pá/so)', 'unit_price' => 150.0, 'qty' => 60],
            ['name' => 'Parkování u centra závodu',      'unit_price' => 50.0,  'qty' => 100],
            ['name' => 'Závodní tričko',                 'unit_price' => 350.0, 'qty' => 40],
        ];

        foreach ($futureEvents as $event) {
            $templates = $faker->randomElements($serviceTemplates, $faker->numberBetween(2, 3));

            foreach ($templates as $template) {
                $service = SportService::factory()->create([
                    'sport_event_id'         => $event->id,
                    'service_name_cz'        => $template['name'],
                    'last_booking_date_time' => Carbon::parse($event->date)->subDays(7)->setTime(23, 59)->format('Y-m-d H:i:s'),
                    'unit_price'             => $template['unit_price'],
                    'qty_available'          => $template['qty'],
                    'qty_already_ordered'    => 0,
                    'qty_remaining'          => $template['qty'],
                ]);

                $paymentDate = SportServicePaymentDate::factory()->create([
                    'sport_service_id'    => $service->id,
                    'payment_date'        => Carbon::parse($event->date)->subDays(3)->toDateString(),
                    'description'         => 'Úhrada z kreditu před závodem',
                    'created_by_user_id'  => $admin->id,
                ]);

                // Orders from a few member race profiles
                $ordered = 0;

                foreach ($profiles->random(min(3, $profiles->count())) as $profile) {
                    $qty = $faker->numberBetween(1, 2);
                    $ordered += $qty;

                    SportServiceOrder::factory()->create([
                        'sport_event_id'                => $event->id,
                        'sport_service_id'              => $service->id,
                        'sport_service_payment_date_id' => $paymentDate->id,
                        'user_id'                       => $profile->user_id,
                        'user_race_profile_id'          => $profile->id,
                        'source_user_id'                => $profile->user_id,
                        'qty'                  => $qty,
                        'unit_price'           => $template['unit_price'],
                        'note'                 => $faker->optional(0.3)->sentence(3),
                        'status'               => $faker->randomElement([
                            ServiceOrderStatus::Ordered,
                            ServiceOrderStatus::Ordered,
                            ServiceOrderStatus::Cancelled,
                        ]),
                    ]);
                }

                $service->update([
                    'qty_already_ordered' => $ordered,
                    'qty_remaining'       => max(0, $template['qty'] - $ordered),
                ]);
            }
        }
    }
}
