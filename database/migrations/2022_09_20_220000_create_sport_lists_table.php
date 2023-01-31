<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sport_lists', function (Blueprint $table) {
            $table->id();
            $table->string('short_name', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_lists');
    }
};
