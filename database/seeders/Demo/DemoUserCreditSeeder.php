<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\SportEvent;
use App\Models\UserCredit;
use App\Models\UserRaceProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoUserCreditSeeder extends Seeder
{
    public function run(): void
    {
        $faker       = \Faker\Factory::create('cs_CZ');
        $profiles    = UserRaceProfile::with('user')->get();
        $pastEvents  = SportEvent::where('date', '<', Carbon::today())->pluck('id')->toArray();

        if ($profiles->isEmpty()) {
            return;
        }

        foreach ($profiles as $profile) {
            $userId = $profile->user_id;

            // Initial deposit
            UserCredit::create([
                'user_id'              => $userId,
                'user_race_profile_id' => $profile->id,
                'amount'               => $faker->randomElement([1000, 1500, 2000, 2500, 3000]),
                'currency'             => UserCredit::CURRENCY_CZK,
                'source'               => UserCredit::SOURCE_USER,
                'status'               => UserCreditStatus::Done->value,
                'credit_type'          => UserCreditType::InitialDeposit->value,
            ]);

            // Membership fee (annual)
            UserCredit::create([
                'user_id'              => $userId,
                'user_race_profile_id' => $profile->id,
                'amount'               => -$faker->randomElement([800, 1000, 1200]),
                'currency'             => UserCredit::CURRENCY_CZK,
                'source'               => UserCredit::SOURCE_CRON,
                'status'               => UserCreditStatus::Done->value,
                'credit_type'          => UserCreditType::MembershipFees->value,
            ]);

            // Race fees for some past events
            $eventSample = $faker->randomElements($pastEvents, min(5, count($pastEvents)));

            foreach ($eventSample as $eventId) {
                UserCredit::create([
                    'user_id'              => $userId,
                    'user_race_profile_id' => $profile->id,
                    'sport_event_id'       => $eventId,
                    'amount'               => -$faker->randomElement([150, 200, 250, 300, 350]),
                    'currency'             => UserCredit::CURRENCY_CZK,
                    'source'               => UserCredit::SOURCE_CRON,
                    'status'               => UserCreditStatus::Done->value,
                    'credit_type'          => UserCreditType::CashOut->value,
                ]);
            }

            // Additional deposit for some users
            if ($faker->boolean(60)) {
                UserCredit::create([
                    'user_id'              => $userId,
                    'user_race_profile_id' => $profile->id,
                    'amount'               => $faker->randomElement([500, 1000, 1500, 2000]),
                    'currency'             => UserCredit::CURRENCY_CZK,
                    'source'               => UserCredit::SOURCE_USER,
                    'status'               => UserCreditStatus::Done->value,
                    'credit_type'          => UserCreditType::UserDonation->value,
                ]);
            }
        }
    }
}
