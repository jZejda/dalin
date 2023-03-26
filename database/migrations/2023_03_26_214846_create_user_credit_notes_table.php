<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('user_credit_id');
            $table->foreign('user_credit_id')->references('id')->on('user_credits');
            $table->unsignedBigInteger('note_user_id');
            $table->foreign('note_user_id')->references('id')->on('users');
            $table->longText('note')->nullable(false);
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'user_credit_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_credit_notes');
    }
};
