<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaceProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            /*
             $table->tinyInteger('sport');
             $table->string('first_name', 255);
             $table->string('sur_name', 255);
             $table->string('reg_number', 255);
             $table->integer('si_number', 12);
             $table->integer('birdth_date', 12);
             $table->string('city', 255);
             $table->string('psc', 255);
             $table->string('mobile'); //TODO blbost musi se najit en ekvivalent
             $table->string('cellular'); //TODO blbost musi se najit en ekvivalent
             $table->string('gender');
             $table->string('license');
            */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_profile');
    }
}
