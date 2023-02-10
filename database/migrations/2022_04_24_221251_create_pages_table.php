<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('content_category_id')->nullable();
            $table->foreign('content_category_id')->references('id')->on('content_categories');
            $table->string('title', 255);
            $table->string('slug')->nullable();
            $table->longText('content');
            $table->tinyInteger('content_format')->default(1);
            $table->string('picture_attachment')->default(255);
            $table->string('status', 10)->default('close');
            $table->tinyInteger('weight')->default(50);
            $table->tinyInteger('page_menu')->default(0)->unsigned();
            $table->timestamps();

            // $table->index(['title']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
