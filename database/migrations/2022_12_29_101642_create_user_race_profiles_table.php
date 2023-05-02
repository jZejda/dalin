<?php

declare(strict_types=1);

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
        Schema::create('user_race_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('reg_number')->unique();
            $table->string('oris_id')->nullable();
            $table->string('club_user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender', 3);
            $table->integer('si')->unsigned()->nullable();
            $table->integer('iof_id')->unsigned()->nullable();

            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('zip')->nullable();

            $table->string('licence_ob', 3)->nullable();
            $table->string('licence_lob', 3)->nullable();
            $table->string('licence_mtbo', 3)->nullable();

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
        Schema::dropIfExists('user_race_profiles');
    }
};
