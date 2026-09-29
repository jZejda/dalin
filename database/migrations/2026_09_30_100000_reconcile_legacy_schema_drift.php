<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Brings databases created before these changes in line with the current schema.
 *
 * The changes below were made by editing the original create_* migrations in place,
 * so databases that had already run them (abm, pbm) never received them. Every step
 * is guarded, so on a fresh database this migration is a no-op.
 */
return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Added to create_sport_classes_table in #89 (relay legs per class).
        if (! Schema::hasColumn('sport_classes', 'legs')) {
            Schema::table('sport_classes', function (Blueprint $table): void {
                $table->tinyInteger('legs')->unsigned()->nullable()->after('controls');
            });
        }

        // Widened from 16 to 32 in create_sport_classes_table in #101 (longer ORIS class names).
        Schema::table('sport_classes', function (Blueprint $table): void {
            $table->string('name', 32)->nullable()->change();
        });

        // Added to create_users_table in 31ec972 (API key lookup by hash).
        if (! Schema::hasIndex('users', 'users_api_key_hash_unique')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->unique('api_key_hash');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Intentionally empty: the columns and index belong to the original create_* migrations,
        // dropping them here would break databases that got them from there.
    }
};
