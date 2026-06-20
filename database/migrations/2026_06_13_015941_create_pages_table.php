<?php

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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('menu_label')->nullable();
            $table->json('meta_description')->nullable();
            $table->json('hero_eyebrow')->nullable();
            $table->json('hero_title');
            $table->json('hero_subtitle')->nullable();
            $table->json('content_blocks')->nullable();
            $table->string('template')->default('default');
            $table->string('status')->default('published')->index();
            $table->boolean('show_in_menu')->default(true);
            $table->unsignedInteger('menu_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
