<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // No-op on fresh installs: the base create-table migration already has these columns.
        if (! Schema::hasColumn('sport_disciplines', 'relays')) {
            Schema::table('sport_disciplines', function (Blueprint $table) {
                $table->string('short_name', 16)->change();
                $table->tinyInteger('relays')->default(0)->after('long_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sport_disciplines', 'relays')) {
            Schema::table('sport_disciplines', function (Blueprint $table) {
                $table->dropColumn('relays');
                $table->string('short_name', 3)->change();
            });
        }
    }
};
