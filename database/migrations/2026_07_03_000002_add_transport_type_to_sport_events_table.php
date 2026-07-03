<?php

declare(strict_types=1);

use App\Enums\SportEventTransportType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sport_events', function (Blueprint $table) {
            $table->string('transport_type')
                ->default(SportEventTransportType::SelfOnly->value)
                ->index()
                ->after('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sport_events', function (Blueprint $table) {
            $table->dropColumn('transport_type');
        });
    }
};
