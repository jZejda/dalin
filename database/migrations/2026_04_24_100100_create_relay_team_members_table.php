<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('relay_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('relay_team_id');
            $table->foreign('relay_team_id')->references('id')->on('relay_teams')->cascadeOnDelete();
            $table->unsignedTinyInteger('slot');
            $table->unsignedBigInteger('user_race_profile_id')->nullable();
            $table->foreign('user_race_profile_id')->references('id')->on('user_race_profiles')->nullOnDelete();
            $table->unsignedBigInteger('user_entry_id')->nullable();
            $table->foreign('user_entry_id')->references('id')->on('user_entries')->nullOnDelete();
            $table->timestamps();

            $table->unique(['relay_team_id', 'slot']);
            $table->unique('user_entry_id');
            $table->index(['relay_team_id', 'user_race_profile_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relay_team_members');
    }
};
