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
        $blocks = is_array($blocks) ? $blocks : [];

        if (collect($blocks)->contains(fn ($block) => data_get($block, 'type') === 'faq_order_form')) {
            return;
        }

        $blocks[] = $this->orderFormBlock();

        DB::table('pages')->where('id', $page->id)->update([
            'content_blocks' => json_encode($blocks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
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

    private function orderFormBlock(): array
    {
        return [
            'type' => 'faq_order_form',
            'heading' => [
                'en' => 'Submit your order / query',
                'es' => 'Envia tu pedido / consulta',
                'de' => 'Senden Sie Ihre Bestellung / Anfrage',
            ],
            'services' => [
                'Holiday rental cleaning',
                'Private home cleaning',
                'Key holding',
                'Laundry service',
                'Property inspection',
                'Airport transfer',
                'Other',
            ],
            'contact_methods' => ['Email', 'WhatsApp', 'Telephone'],
        ];
    }
};
