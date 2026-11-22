<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The primary chip stays in user_race_profiles.si; sport/type use ORIS numbering (0 = all).
        Schema::table('user_race_profiles', function (Blueprint $table): void {
            $table->unsignedTinyInteger('si_sport')->default(0)->after('si');
            $table->unsignedTinyInteger('si_type')->default(0)->after('si_sport');
        });

        // Additional chips (ORIS allows 3 chips per person: the primary one + max 2 extra).
        Schema::create('user_race_profile_chips', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_race_profile_id')->constrained('user_race_profiles')->cascadeOnDelete();
            $table->unsignedInteger('si');
            $table->unsignedTinyInteger('sport')->default(0);
            $table->unsignedTinyInteger('type')->default(0);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_race_profile_chips');

        Schema::table('user_race_profiles', function (Blueprint $table): void {
            $table->dropColumn(['si_sport', 'si_type']);
        });
    }
};
