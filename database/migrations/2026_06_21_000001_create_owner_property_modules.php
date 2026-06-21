<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('google_photo_album_link')->nullable();
            $table->string('owner_user_id')->unique();
            $table->string('owner_password');
            $table->timestamps();
        });

        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('Tourist Rental')->index();
            $table->string('other_type')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->boolean('laundry_included')->default(false);
            $table->boolean('check_in_included')->default(false);
            $table->boolean('cleaning_included')->default(false);
            $table->boolean('management_included')->default(false);
            $table->boolean('full_service_included')->default(false);
            $table->decimal('price_per_service', 10, 2)->nullable();
            $table->decimal('annual_price', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('property_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('number_of_guests')->default(1);
            $table->string('guest_name');
            $table->string('telephone')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->dateTime('visiting_at');
            $table->string('visitor_name');
            $table->text('observation')->nullable();
            $table->text('activity_performed')->nullable();
            $table->time('exit_time')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('holiday_homes', function (Blueprint $table) {
            $table->id();
            $table->string('area_name');
            $table->string('name');
            $table->unsignedInteger('number_of_bedrooms')->default(1);
            $table->unsignedInteger('maximum_number_of_guests')->default(1);
            $table->string('online_booking_link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holiday_homes');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('property_reservations');
        Schema::dropIfExists('properties');
        Schema::dropIfExists('owners');
    }
};
