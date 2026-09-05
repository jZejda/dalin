<?php

declare(strict_types=1);

use App\Enums\BadgeColor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('badge_color', 20)->default(BadgeColor::Blue->value)->after('locale');
            $table->string('avatar_path')->nullable()->after('badge_color');
        });

        // Randomly assign a badge color to every existing user; new users get one
        // from User::assignRandomBadgeColor() (see #[Boot] in the model).
        $colors = array_map(fn (BadgeColor $color): string => $color->value, BadgeColor::cases());

        DB::table('users')->orderBy('id')->select('id')->chunkById(200, function ($users) use ($colors) {
            foreach ($users as $user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['badge_color' => $colors[array_rand($colors)]]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['badge_color', 'avatar_path']);
        });
    }
};
