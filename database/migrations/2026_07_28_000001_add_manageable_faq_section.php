<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $faqBlock = $this->faqBlock();
        $page = DB::table('pages')->where('slug', 'faq')->first();

        if ($page) {
            $blocks = json_decode($page->content_blocks ?: '[]', true);
            $blocks = is_array($blocks) ? $blocks : [];
            $existingFaqBlock = collect($blocks)->first(fn ($block) => data_get($block, 'type') === 'faq_section');

            $blocks = [$existingFaqBlock ?: $faqBlock];

            DB::table('pages')->where('id', $page->id)->update([
                'title' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
                'menu_label' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
                'meta_description' => json_encode([
                    'en' => 'Find quick answers about Santana Prime property care, cleaning, key holding, and holiday rental management.',
                    'es' => 'Encuentre respuestas rapidas sobre cuidado de propiedades, limpieza, custodia de llaves y gestion de alquiler vacacional de Santana Prime.',
                ]),
                'hero_title' => json_encode(['en' => 'Frequently asked questions', 'es' => 'Preguntas frecuentes']),
                'hero_subtitle' => json_encode([
                    'en' => 'Find quick answers about Santana Prime property care, cleaning, key holding, and holiday rental management.',
                    'es' => 'Encuentre respuestas rapidas sobre cuidado de propiedades, limpieza, custodia de llaves y gestion de alquiler vacacional.',
                ]),
                'content_blocks' => json_encode($blocks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'template' => 'prime',
                'status' => 'published',
                'show_in_menu' => true,
                'updated_at' => $now,
            ]);
        } else {
            DB::table('pages')->insert([
                'slug' => 'faq',
                'title' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
                'menu_label' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
                'meta_description' => json_encode([
                    'en' => 'Find quick answers about Santana Prime property care, cleaning, key holding, and holiday rental management.',
                    'es' => 'Encuentre respuestas rapidas sobre cuidado de propiedades, limpieza, custodia de llaves y gestion de alquiler vacacional de Santana Prime.',
                ]),
                'hero_eyebrow' => json_encode(['en' => 'Hola Santana', 'es' => 'Hola Santana']),
                'hero_title' => json_encode(['en' => 'Frequently asked questions', 'es' => 'Preguntas frecuentes']),
                'hero_subtitle' => json_encode([
                    'en' => 'Find quick answers about Santana Prime property care, cleaning, key holding, and holiday rental management.',
                    'es' => 'Encuentre respuestas rapidas sobre cuidado de propiedades, limpieza, custodia de llaves y gestion de alquiler vacacional.',
                ]),
                'content_blocks' => json_encode([$faqBlock], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'template' => 'prime',
                'status' => 'published',
                'show_in_menu' => true,
                'menu_order' => 9,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $page = DB::table('pages')->where('slug', 'faq')->first();
        }

        if ($page) {
            DB::table('menu_items')->updateOrInsert(
                ['page_id' => $page->id],
                [
                    'label' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
                    'url' => null,
                    'sort_order' => 9,
                    'is_active' => true,
                    'target' => '_self',
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }

    public function down(): void
    {
        $page = DB::table('pages')->where('slug', 'faq')->first();

        if (! $page) {
            return;
        }

        $blocks = json_decode($page->content_blocks ?: '[]', true);
        $blocks = collect(is_array($blocks) ? $blocks : [])
            ->reject(fn ($block) => data_get($block, 'type') === 'faq_section')
            ->values()
            ->all();

        DB::table('pages')->where('id', $page->id)->update([
            'content_blocks' => json_encode($blocks, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
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
                [
                    'question' => ['en' => 'Can you manage a property while I am away?', 'es' => 'Podeis gestionar una propiedad mientras estoy fuera?'],
                    'answer' => ['en' => 'Yes. We offer key holding, regular inspections, maintenance coordination, cleaning, laundry, and owner updates so the property stays ready and cared for.', 'es' => 'Si. Ofrecemos custodia de llaves, inspecciones periodicas, coordinacion de mantenimiento, limpieza, lavanderia y actualizaciones para propietarios.'],
                ],
                [
                    'question' => ['en' => 'Do you handle holiday rental guest support?', 'es' => 'Gestionais la atencion a huespedes de alquiler vacacional?'],
                    'answer' => ['en' => 'Yes. We can help with guest communication, check-in support, cleaning between stays, restocking, and property preparation.', 'es' => 'Si. Podemos ayudar con comunicacion con huespedes, apoyo en check-in, limpieza entre estancias, reposicion y preparacion de la propiedad.'],
                ],
                [
                    'question' => ['en' => 'Can I request only cleaning or laundry?', 'es' => 'Puedo solicitar solo limpieza o lavanderia?'],
                    'answer' => ['en' => 'Yes. Services can be ordered individually or combined into a regular management plan.', 'es' => 'Si. Los servicios pueden contratarse individualmente o combinarse en un plan de gestion regular.'],
                ],
                [
                    'question' => ['en' => 'How do I request a quote?', 'es' => 'Como solicito un presupuesto?'],
                    'answer' => ['en' => 'Use the enquiry form on this page or contact us by WhatsApp. Tell us the property type, location, and service needed, and we will reply with the next steps.', 'es' => 'Use el formulario de consulta de esta pagina o contactenos por WhatsApp. Indiquenos el tipo de propiedad, ubicacion y servicio necesario, y responderemos con los siguientes pasos.'],
                ],
            ],
        ];
    }
};
