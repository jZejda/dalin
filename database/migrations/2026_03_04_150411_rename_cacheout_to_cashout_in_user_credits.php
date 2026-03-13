<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('user_credits')
            ->where('credit_type', 'cacheOut')
            ->update(['credit_type' => 'cashOut']);
    }

    public function down(): void
    {
        DB::table('user_credits')
            ->where('credit_type', 'cashOut')
            ->update(['credit_type' => 'cacheOut']);
    }
};
