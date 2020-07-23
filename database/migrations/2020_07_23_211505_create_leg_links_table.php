<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leg_links', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('oevent_leg_id')->unsigned()->nullable();
            $table->foreign('oevent_leg_id')->references('id')->on('oevent_legs')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('oevent_link_type_id')->unsigned()->nullable();
            $table->foreign('oevent_link_type_id')->references('id')->on('oevent_link_types')->onUpdate('cascade')->onDelete('cascade');

            $table->json('link_setting')->nullable();
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
        Schema::dropIfExists('leg_links');
    }
}
