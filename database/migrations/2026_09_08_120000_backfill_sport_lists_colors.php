<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The 2026_09_06_100000 migration added `sport_lists.color` with a single default
 * ('#3388FF') for the whole column, so every row that already existed at deploy
 * time (any environment migrated before SportListsSeeder was re-run) ended up
 * blue instead of its intended per-sport color. Re-running the seeder is not an
 * option — SportList::create() would duplicate the rows. Backfill by short_name
 * instead, matching database/seeders/SportListsSeeder.php (see docs/map-icons.md).
 */
return new class () extends Migration {
    public function up(): void
    {
        $colors = [
            'OB'    => '#FF8C00',
            'LOB'   => '#1565C0',
            'MTBO'  => '#2E7D32',
            'TRAIL' => '#6A1B9A',
        ];

        foreach ($colors as $shortName => $color) {
            DB::table('sport_lists')
                ->where('short_name', $shortName)
                ->update(['color' => $color]);
        }
    }

    public function down(): void
    {
        DB::table('sport_lists')->update(['color' => '#3388FF']);
    }
};
