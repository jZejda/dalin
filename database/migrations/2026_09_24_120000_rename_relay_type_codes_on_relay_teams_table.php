<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ORIS renumbered and renamed its disciplines; relay_type now follows the new ORIS short names
 * (ST → RE, SS → SR, DR → TE), matching App\Enums\RelayType.
 */
return new class () extends Migration {
    /** @var array<string, string> */
    private const array MAP = [
        'ST' => 'RE',
        'SS' => 'SR',
        'DR' => 'TE',
    ];

    public function up(): void
    {
        foreach (self::MAP as $old => $new) {
            DB::table('relay_teams')->where('relay_type', $old)->update(['relay_type' => $new]);
        }

        // Any other legacy value would break the enum cast — fall back to a plain relay.
        DB::table('relay_teams')
            ->whereNotIn('relay_type', array_values(self::MAP))
            ->update(['relay_type' => 'RE']);

        Schema::table('relay_teams', function (Blueprint $table) {
            $table->string('relay_type', 8)->default('RE')->change();
        });
    }

    public function down(): void
    {
        foreach (self::MAP as $old => $new) {
            DB::table('relay_teams')->where('relay_type', $new)->update(['relay_type' => $old]);
        }

        Schema::table('relay_teams', function (Blueprint $table) {
            $table->string('relay_type', 8)->default('ST')->change();
        });
    }
};
