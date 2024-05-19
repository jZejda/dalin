<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->string('type')->nullable();
            $table->dateTime('date')->nullable();
            $table->float('amount')->nullable();
            $table->string('variable_symbol')->nullable();
            $table->string('specific_symbol')->nullable();
            $table->string('constant_symbol')->nullable();
            $table->string('description')->nullable();
            $table->string('note')->nullable();
            $table->string('error')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
