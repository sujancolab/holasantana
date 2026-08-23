<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $blocks = [
            [
                'type' => 'faq_order_form',
                'heading' => ['en' => 'Submit your order / query', 'es' => 'Envia tu pedido / consulta'],
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
            ],
            [
                'type' => 'contact',
                'heading' => [
                    'en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.",
                    'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.',
                ],
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
            ],
        ];

        $pageData = [
            'title' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
            'menu_label' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
            'meta_description' => json_encode([
                'en' => 'Send your Santana Prime service request with your preferred date, time, and contact method.',
                'es' => 'Envia tu solicitud de servicio de Santana Prime con fecha, hora y contacto preferido.',
            ]),
            'hero_eyebrow' => json_encode(['en' => 'Hola Santana', 'es' => 'Hola Santana']),
            'hero_title' => json_encode(['en' => 'Submit your order / query', 'es' => 'Envia tu pedido / consulta']),
            'hero_subtitle' => json_encode(['en' => 'Tell us what you need and our team will contact you.', 'es' => 'Cuentanos que necesitas y nuestro equipo te contactara.']),
            'content_blocks' => json_encode($blocks),
            'template' => 'prime',
            'status' => 'published',
            'show_in_menu' => true,
            'menu_order' => 9,
            'updated_at' => $now,
        ];

        if (DB::table('pages')->where('slug', 'faq')->exists()) {
            DB::table('pages')->where('slug', 'faq')->update($pageData);
        } else {
            DB::table('pages')->insert(['slug' => 'faq', 'created_at' => $now] + $pageData);
        }

        $pageId = DB::table('pages')->where('slug', 'faq')->value('id');

        $menuData = [
            'label' => json_encode(['en' => 'FAQ', 'es' => 'FAQ']),
            'url' => null,
            'sort_order' => 9,
            'is_active' => true,
            'target' => '_self',
            'updated_at' => $now,
        ];

        if (DB::table('menu_items')->where('page_id', $pageId)->exists()) {
            DB::table('menu_items')->where('page_id', $pageId)->update($menuData);
        } else {
            DB::table('menu_items')->insert(['page_id' => $pageId, 'created_at' => $now] + $menuData);
        }
    }

    public function down(): void
    {
        $pageId = DB::table('pages')->where('slug', 'faq')->value('id');

        if ($pageId) {
            DB::table('menu_items')->where('page_id', $pageId)->delete();
            DB::table('pages')->where('id', $pageId)->delete();
        }
    }
};
