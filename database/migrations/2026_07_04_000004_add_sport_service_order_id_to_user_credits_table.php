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
        Schema::table('user_credits', function (Blueprint $table) {
            $table->foreignId('sport_service_order_id')
                ->nullable()
                ->after('sport_service_id')
                ->constrained('sport_service_orders')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_credits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sport_service_order_id');
        });
    }
};
