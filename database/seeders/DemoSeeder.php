<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\BadgeColor;
use App\Models\User;
use Database\Seeders\Demo\DemoAppSettingsSeeder;
use Database\Seeders\Demo\DemoBankAccountSeeder;
use Database\Seeders\Demo\DemoClubsSeeder;
use Database\Seeders\Demo\DemoBankTransactionSeeder;
use Database\Seeders\Demo\DemoContentSeeder;
use Database\Seeders\Demo\DemoMailLogSeeder;
use Database\Seeders\Demo\DemoMarketplaceSeeder;
use Database\Seeders\Demo\DemoPostSeeder;
use Database\Seeders\Demo\DemoRelaySeeder;
use Database\Seeders\Demo\DemoSportEventExtrasSeeder;
use Database\Seeders\Demo\DemoSportEventSeeder;
use Database\Seeders\Demo\DemoSportServiceSeeder;
use Database\Seeders\Demo\DemoTransportSeeder;
use Database\Seeders\Demo\DemoUserCreditSeeder;
use Database\Seeders\Demo\DemoUserEntrySeeder;
use Database\Seeders\Demo\DemoUserRaceProfileSeeder;
use Database\Seeders\Demo\DemoUsersSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Structural seeders
        $this->call([
            PermissionsSeeder::class,
            SportListsSeeder::class,
            SportDisciplinesSeeder::class,
            SportRegionsSeeder::class,
            SportLevelSeeder::class,
            UserRolesSeeder::class,
            DemoClubsSeeder::class,
            DemoAppSettingsSeeder::class,
        ]);

        // Demo admin user with known demo password
        DB::table('users')->insert([
            'name'       => 'Admin Demo',
            'email'      => 'admin@demo.cz',
            'password'   => bcrypt((string) config('demo.admin_password', 'Demo2026!')),
            'active'     => true,
            'badge_color' => BadgeColor::random()->value,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        $admin = User::where('email', 'admin@demo.cz')->first();
        $admin?->assignRole('super_admin');

        // Demo sport class definitions (basic OB categories, sport_id=1)
        $classes = [
            ['name' => 'H21', 'gender' => 'M', 'age_from' => 21, 'age_to' => 34, 'sport_id' => 1],
            ['name' => 'H35', 'gender' => 'M', 'age_from' => 35, 'age_to' => 44, 'sport_id' => 1],
            ['name' => 'H45', 'gender' => 'M', 'age_from' => 45, 'age_to' => 54, 'sport_id' => 1],
            ['name' => 'H55', 'gender' => 'M', 'age_from' => 55, 'age_to' => 64, 'sport_id' => 1],
            ['name' => 'H65', 'gender' => 'M', 'age_from' => 65, 'age_to' => 99, 'sport_id' => 1],
            ['name' => 'D21', 'gender' => 'F', 'age_from' => 21, 'age_to' => 34, 'sport_id' => 1],
            ['name' => 'D35', 'gender' => 'F', 'age_from' => 35, 'age_to' => 44, 'sport_id' => 1],
            ['name' => 'D45', 'gender' => 'F', 'age_from' => 45, 'age_to' => 54, 'sport_id' => 1],
            ['name' => 'D55', 'gender' => 'F', 'age_from' => 55, 'age_to' => 99, 'sport_id' => 1],
            ['name' => 'H18', 'gender' => 'M', 'age_from' => 16, 'age_to' => 20, 'sport_id' => 1],
            ['name' => 'D18', 'gender' => 'F', 'age_from' => 16, 'age_to' => 20, 'sport_id' => 1],
            ['name' => 'HDR', 'gender' => null, 'age_from' => 0, 'age_to' => 99, 'sport_id' => 1],
        ];

        foreach ($classes as $class) {
            DB::table('sport_class_definitions')->insert([
                'sport_id'   => $class['sport_id'],
                'name'       => $class['name'],
                'gender'     => $class['gender'],
                'age_from'   => $class['age_from'],
                'age_to'     => $class['age_to'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);
        }

        // Demo data seeders
        $this->call([
            DemoUsersSeeder::class,
            DemoSportEventSeeder::class,
            DemoUserRaceProfileSeeder::class,
            DemoBankAccountSeeder::class,
            DemoBankTransactionSeeder::class,
            DemoUserEntrySeeder::class,
            DemoUserCreditSeeder::class,
            DemoPostSeeder::class,
            DemoContentSeeder::class,
            DemoMarketplaceSeeder::class,
            DemoTransportSeeder::class,
            DemoSportServiceSeeder::class,
            // Relays first: their events must exist before the extras seeder
            // hangs map markers on them
            DemoRelaySeeder::class,
            DemoSportEventExtrasSeeder::class,
            DemoMailLogSeeder::class,
        ]);
    }
}
