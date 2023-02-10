<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('abbr', 3)->unique();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('region_id');
            $table->foreign('region_id')->references('id')->on('sport_regions');
            $table->string('oris_id')->nullable();
            $table->string('oris_number')->nullable();
            $table->timestamps();

            //$table->index(['abbr', 'name']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clubs');
    }
};
