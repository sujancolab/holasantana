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
        $faqBlock = collect($blocks)->first(fn ($block) => data_get($block, 'type') === 'faq_section') ?: $this->faqBlock();

        DB::table('pages')->where('id', $page->id)->update([
            'content_blocks' => json_encode([$faqBlock], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }

    private function faqBlock(): array
    {
        return [
            'type' => 'faq_section',
            'heading' => ['en' => 'Frequently asked questions', 'es' => 'Preguntas frecuentes'],
            'body' => [
                'en' => 'Answers to the questions owners and guests ask us most often.',
                'es' => 'Respuestas a las preguntas que propietarios y huespedes nos hacen con mas frecuencia.',
            ],
            'faqs' => [
                [
                    'question' => ['en' => 'What areas do you cover?', 'es' => 'En que zonas trabajais?'],
                    'answer' => ['en' => 'We work in Torrevieja, Orihuela Costa, La Mata, Guardamar, Punta Prima, Playa Flamenca, Cabo Roig, Los Altos, and nearby Costa Blanca areas.', 'es' => 'Trabajamos en Torrevieja, Orihuela Costa, La Mata, Guardamar, Punta Prima, Playa Flamenca, Cabo Roig, Los Altos y zonas cercanas de la Costa Blanca.'],
                ],
            ],
        ];
    }
};
