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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
            $table->string('transaction_indicator', 64)->index();
            $table->dateTime('date');
            $table->float('amount');
            $table->string('currency');
            $table->string('bank_account_identifier', 128)->nullable();
            $table->string('external_key', 128)->index();
            $table->string('variable_symbol', 64)->nullable()->index();
            $table->string('specific_symbol', 64)->nullable();
            $table->string('constant_symbol', 64)->nullable();
            $table->string('description')->nullable();
            $table->string('note')->nullable();
            $table->string('error')->nullable();
            $table->string('status', 16)->nullable()->index();
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
