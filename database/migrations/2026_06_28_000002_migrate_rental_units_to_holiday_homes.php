<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $page = DB::table('pages')->where('slug', 'home-rental')->first();

        if (! $page || empty($page->content_blocks)) {
            return;
        }

        $blocks = is_string($page->content_blocks)
            ? json_decode($page->content_blocks, true)
            : $page->content_blocks;

        if (! is_array($blocks)) {
            return;
        }

        $sortOrder = 10;

        foreach ($blocks as $block) {
            if (($block['type'] ?? null) !== 'rental_unit') {
                continue;
            }

            $name = data_get($block, 'heading.en');

            if (! $name || DB::table('holiday_homes')->where('name', $name)->exists()) {
                continue;
            }

            [$bedrooms, $guests] = $this->occupancyFor($name);

            DB::table('holiday_homes')->insert([
                'area_name' => str_contains(strtolower($name), 'salinas') ? 'Salinas' : 'Torrevieja',
                'name' => $name,
                'image_url' => data_get($block, 'images.0'),
                'description' => data_get($block, 'body.en'),
                'number_of_bedrooms' => $bedrooms,
                'maximum_number_of_guests' => $guests,
                'online_booking_link' => data_get($block, 'actions.0.url'),
                'is_active' => true,
                'sort_order' => $sortOrder,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sortOrder += 10;
        }
    }

    public function down(): void
    {
        //
    }

    private function occupancyFor(string $name): array
    {
        $normalized = strtolower($name);

        if (str_contains($normalized, 'salinas')) {
            return [2, 4];
        }

        if (str_contains($normalized, 'studio')) {
            return [0, 2];
        }

        return [1, 4];
    }
};
