<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'google_photo_link')) {
                $table->string('google_photo_link', 500)->nullable()->after('address');
            }
        });

        if (Schema::hasColumn('owners', 'google_photo_album_link')) {
            DB::table('owners')
                ->whereNotNull('google_photo_album_link')
                ->orderBy('id')
                ->each(function (object $owner): void {
                    DB::table('properties')
                        ->where('owner_id', $owner->id)
                        ->whereNull('google_photo_link')
                        ->update(['google_photo_link' => $owner->google_photo_album_link]);
                });

            Schema::table('owners', function (Blueprint $table) {
                $table->dropColumn('google_photo_album_link');
            });
        }
    }

    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            if (! Schema::hasColumn('owners', 'google_photo_album_link')) {
                $table->string('google_photo_album_link')->nullable()->after('google_activity_list_link');
            }
        });

        if (Schema::hasColumn('properties', 'google_photo_link')) {
            DB::table('properties')
                ->whereNotNull('google_photo_link')
                ->orderBy('id')
                ->each(function (object $property): void {
                    DB::table('owners')
                        ->where('id', $property->owner_id)
                        ->whereNull('google_photo_album_link')
                        ->update(['google_photo_album_link' => $property->google_photo_link]);
                });

            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('google_photo_link');
            });
        }
    }
};
