<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_homes', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('name');
            $table->text('description')->nullable()->after('image_url');
            $table->boolean('is_active')->default(true)->after('online_booking_link');
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_homes', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'description', 'is_active', 'sort_order']);
        });
    }
};
