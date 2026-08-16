<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            if (! Schema::hasColumn('owners', 'google_activity_list_link')) {
                $table->string('google_activity_list_link')->nullable()->after('whatsapp');
            }
        });

        DB::table('owners')
            ->whereNull('google_activity_list_link')
            ->whereNotNull('google_photo_album_link')
            ->update([
                'google_activity_list_link' => DB::raw('google_photo_album_link'),
                'google_photo_album_link' => null,
            ]);
    }

    public function down(): void
    {
        DB::table('owners')
            ->whereNull('google_photo_album_link')
            ->whereNotNull('google_activity_list_link')
            ->update([
                'google_photo_album_link' => DB::raw('google_activity_list_link'),
            ]);

        Schema::table('owners', function (Blueprint $table) {
            if (Schema::hasColumn('owners', 'google_activity_list_link')) {
                $table->dropColumn('google_activity_list_link');
            }
        });
    }
};
