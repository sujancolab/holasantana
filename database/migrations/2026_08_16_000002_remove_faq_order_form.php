<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $page = DB::table('pages')->where('slug', 'faq')->first();

        if (! $page) {
            return;
        }

        $blocks = json_decode($page->content_blocks ?: '[]', true);
        $blocks = collect(is_array($blocks) ? $blocks : [])
            ->reject(fn ($block) => data_get($block, 'type') === 'faq_order_form')
            ->values()
            ->all();

        DB::table('pages')->where('id', $page->id)->update([
            'content_blocks' => json_encode($blocks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
