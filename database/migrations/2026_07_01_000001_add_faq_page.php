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
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
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
