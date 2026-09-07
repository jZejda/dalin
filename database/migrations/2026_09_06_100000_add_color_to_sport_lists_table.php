<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('sport_lists', function (Blueprint $table) {
            $table->string('color', 7)->default('#3388FF')->after('short_name');
        });
    }

    public function down(): void
    {
        Schema::table('sport_lists', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
