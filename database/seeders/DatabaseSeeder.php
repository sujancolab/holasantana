<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@holasantana.com'],
            [
                'name' => 'Hola Santana Admin',
                'role' => 'admin',
                'password' => Hash::make('Admin@12345'),
            ],
        );

        $pages = [
            ['home', 'Welcome', 'Inicio', 'Real estate and holiday rental management in Gran Canaria', 'Property care, guest services, and holiday homes managed with local attention.', '/'],
            ['general-4', 'Management of tourist rental', 'Gestion de alquiler turistico', 'Tourist rental management', 'From listing preparation to guest communication, keep your tourist rental moving smoothly.', '/general-4'],
            ['projects-6', 'Management of private home', 'Gestion de vivienda privada', 'Private home management', 'Reliable support for owners who want their home cared for while they are away.', '/projects-6'],
            ['category/tourist-rental-service', 'Buy our services', 'Comprar nuestro servicio', 'Buy our services', 'Choose the service level that fits your rental property and ownership goals.', '/category/tourist-rental-service'],
            ['home-rental', 'Rent our holiday home', 'Alquila nuestra casa vacacional', 'Rent our holiday home', 'Explore available holiday homes and plan a comfortable stay in the islands.', '/home'],
            ['about-3', 'About', 'Sobre nosotros', 'About Hola Santana', 'A local team helping homeowners and travelers with practical, personal service.', '/about-3'],
            ['contact', 'Contact', 'Contacto', 'Contact Hola Santana', 'Send an enquiry and the team will help with the right next step.', '/contact'],
            ['blog', 'Blog', 'Blog', 'Blog', 'Guides, updates, and notes for holiday rentals, property owners, and guests.', '/blog'],
        ];

        foreach ($pages as $index => [$slug, $en, $es, $hero, $subtitle]) {
            $isHome = $slug === 'home';
            $isTouristRental = $slug === 'general-4';
            $isPrivateHome = $slug === 'projects-6';
            $isTouristRentalCategory = $slug === 'category/tourist-rental-service';
            $isHolidayRental = $slug === 'home-rental';
            $isAbout = $slug === 'about-3';
            $isContact = $slug === 'contact';
            $isBlog = $slug === 'blog';

            $heroTitleEn = match (true) {
                $isHome => 'Earn a profit on your property easily and safely',
                $isTouristRental => 'Stress-Free Holiday Rental Management - We handle cleaning, guests, laundry & maintenance',
                $isPrivateHome => 'Management of your seasonal / second home-Vila',
                $isTouristRentalCategory => 'Santana Prime',
                $isHolidayRental => 'Welcome to the Holiday Home Santana',
                $isAbout => 'About Santana Prime - Home Care and Tourist Rental Management Service',
                $isContact => 'Santana Prime - Home care and Tourist rental management services',
                $isBlog => 'All Posts',
                default => $hero,
            };

            $heroSubtitleEn = match (true) {
                $isHome => 'Management services for seasonal home/villa/bungalow and holiday homes',
                $isTouristRental => 'Extensive Experience in Short-Term Rental & Airbnb Management',
                $isPrivateHome => 'Property Management and Trusted Key Holding Service',
                $isTouristRentalCategory => 'Servicios para viviendas turisticas',
                $isHolidayRental => 'Your ideal retreat on the Costa Blanca in Torrevieja',
                $isAbout => 'Santana Prime provides professional home management and holiday rental services in Torrevieja.',
                $isContact => 'Experience in short-term rental management and Airbnb services',
                $isBlog => 'All Posts',
                default => $subtitle,
            };

            $contentBlocks = match (true) {
                $isHome => $this->homeBlocks(),
                $isTouristRental => $this->touristRentalBlocks(),
                $isPrivateHome => $this->privateHomeBlocks(),
                $isTouristRentalCategory => $this->touristRentalCategoryBlocks(),
                $isHolidayRental => $this->holidayRentalBlocks(),
                $isAbout => $this->aboutBlocks(),
                $isContact => $this->contactBlocks(),
                $isBlog => $this->blogBlocks(),
                default => $this->defaultBlocks($slug),
            };

            $template = $isHome ? 'home' : (($isTouristRental || $isPrivateHome || $isTouristRentalCategory || $isHolidayRental || $isAbout || $isContact || $isBlog) ? 'prime' : 'default');

            $page = Page::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => ['en' => $en, 'es' => $es],
                    'menu_label' => ['en' => $en, 'es' => $es],
                    'meta_description' => ['en' => $subtitle, 'es' => $subtitle],
                    'hero_eyebrow' => ['en' => $isHome ? 'Santana Prime' : 'Hola Santana', 'es' => $isHome ? 'Santana Prime' : 'Hola Santana'],
                    'hero_title' => ['en' => $heroTitleEn, 'es' => $isHome ? 'Gana rentabilidad con tu propiedad de forma facil y segura' : $es],
                    'hero_subtitle' => ['en' => $heroSubtitleEn, 'es' => $isHome ? 'Servicios de gestion para viviendas de temporada, villas, bungalows y casas vacacionales' : $subtitle],
                    'content_blocks' => $contentBlocks,
                    'template' => $template,
                    'status' => 'published',
                    'show_in_menu' => true,
                    'menu_order' => $index + 1,
                ],
            );

            MenuItem::updateOrCreate(
                ['page_id' => $page->id],
                [
                    'label' => $page->menu_label,
                    'sort_order' => $page->menu_order,
                    'is_active' => true,
                ],
            );
        }
    }

    private function defaultBlocks(string $slug): array
    {
        return [
            [
                'heading' => ['en' => 'Manage this section from admin', 'es' => 'Gestiona esta seccion desde admin'],
                'body' => ['en' => 'This seeded content is ready to replace with exact website copy, images, calls to action, and translated page sections from Page Management.', 'es' => 'Este contenido inicial se puede reemplazar desde Page Management con textos, imagenes, llamadas a la accion y traducciones.'],
                'button_text' => ['en' => $slug === 'contact' ? 'Contact us' : 'Learn more', 'es' => $slug === 'contact' ? 'Contactanos' : 'Mas informacion'],
                'button_url' => $slug === 'contact' ? 'mailto:info@holasantana.com' : '',
            ],
        ];
    }

    private function touristRentalBlocks(): array
    {
        return [
            [
                'type' => 'gallery',
                'images' => [
                    'https://static.wixstatic.com/media/11062b_e59bd196cba4410c9e6066e800d74d6d~mv2_d_3456_5184_s_4_2.jpg/v1/fill/w_776,h_600,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Modern%20Living%20Room.jpg',
                    'https://static.wixstatic.com/media/91493d838ac447d19ede2426f503cc8d.jpg/v1/fill/w_964,h_600,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Modern%20Bedroom%20Interior.jpg',
                    'https://static.wixstatic.com/media/feb654e11d3a49daa79c16b483bee805.jpg/v1/fill/w_800,h_600,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/feb654e11d3a49daa79c16b483bee805.jpg',
                ],
            ],
            [
                'type' => 'open_intro',
                'body' => ['en' => "Santana Prime specializes in the comprehensive management of vacation rentals, offering exceptional service that ensures the satisfaction of each guest. At our company, we use biodegradable and allergen-free products, along with state-of-the-art equipment, all managed by a team of highly trained experts, committed to providing an efficient and environmentally friendly service.\n\nIn addition, we provide laundry services, key delivery and reception of guests for property demonstrations, ensuring a memorable experience for our clients.\n\nWe also offer additional options, such as reservation management and airport transfers, with the aim of optimising visitors' stay as much as possible.\n\nOur aim is to offer a unique service and make our clients' stay as comfortable and pleasant as possible."],
                'footer' => ['en' => 'All our services:'],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Why choose our cleaning services?'],
                'images' => [
                    'https://static.wixstatic.com/media/c50f24_0ccb27a5b3bb409a8955266883af6aeb~mv2.png/v1/fill/w_536,h_732,al_c,q_90,usm_0.66_1.00_0.01,enc_avif,quality_auto/Office_clean.png',
                    'https://static.wixstatic.com/media/11062b_9fa0a758b5c74abe8b22d362456644ee~mv2.jpg/v1/fill/w_678,h_732,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Cleaning%20Team%20Portrait.jpg',
                ],
                'body' => ['en' => "Excellence in every detail: Our cleaners pride themselves on their attention to detail, ensuring a level of excellence that exceeds expectations.\n\nTailored to your needs: Our cleaning services are flexible and tailored to your specific needs, whether it's preparing for your guests or maintaining your own space.\n\nTime-saving solutions: Enjoy more free time without compromising on cleaning. Our efficient cleaning services allow you to focus on what matters most.\n\nElevate your living experience with our professional cleaning services. Immerse yourself in a world of cleanliness and relaxation, where every detail is carefully taken care of."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Why choose our check-in and check-out service?'],
                'images' => [
                    'https://static.wixstatic.com/media/11062b_aaa3d7aa68764820a4aa29af761f235e~mv2.jpg/v1/fill/w_742,h_496,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Key%20Lock.jpg',
                    'https://static.wixstatic.com/media/11062b_efb3adc854344396b2b2b4ddc9ef7f69~mv2.jpg/v1/fill/w_656,h_496,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Handing%20Over%20Keys.jpg',
                ],
                'body' => ['en' => "Peace of mind: Entrust the logistics to our experienced team, allowing you to focus on what matters most.\n\nProfessional Presentation: Make a lasting impression with a well-organized and professional check-in and check-out process.\n\nTime Saving: Streamline your property management tasks and save valuable time with our efficient services."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Laundry service'],
                'images' => [
                    'https://static.wixstatic.com/media/5fd4767141624c128a6f2fe9df273f7f.jpg/v1/fill/w_700,h_502,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Vintage%20Laundry%20Machines.jpg',
                    'https://static.wixstatic.com/media/11062b_84511c3f4d9a4ab4b699c4087b28618b~mv2.jpg/v1/fill/w_680,h_502,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Hotel%20Laundry%20Room.jpg',
                ],
                'body' => ['en' => "Effortless laundry: Your bed linen and towels can be washed and ironed at our own laundry station without lifting a finger.\n\nFor just EUR 15 per booking (provided you provide spare sets of sheets and towels), our full laundry service (we deliver sets of sheets and towels) EUR 20 ensures your living spaces remain immaculate and inviting for the next guests."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'General maintenance and repairs'],
                'images' => [
                    'https://static.wixstatic.com/media/11062b_0bb0795bdb324557b31f13c496848d31~mv2.jpeg/v1/fill/w_690,h_576,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Pipe%20Repair%20Close-Up.jpeg',
                    'https://static.wixstatic.com/media/c72cc3075b6b4cb0a4b5691df7a374e9.jpg/v1/fill/w_690,h_576,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Air%20Conditioner%20Maintenance.jpg',
                ],
                'body' => ['en' => "Your comfort is our top priority.\nWe are proud to offer a wide range of maintenance services.\nWe understand that every home is unique.\nWe have your back when it comes to basic home maintenance, preventative checkups, painting, and much more."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Additional service'],
                'images' => [
                    'https://static.wixstatic.com/media/0ec9843575814a96a9191e950a8b8f9a.jpg/v1/fill/w_710,h_618,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Signing%20A%20Document.jpg',
                    'https://static.wixstatic.com/media/fabcbe0da8494d1db64d284d3336461f.jpg/v1/fill/w_676,h_618,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Hand%20Stamping%20Document.jpg',
                ],
                'body' => ['en' => "We can also help you with:\n\nObtaining a NIE number and opening a bank account\nNotarial and tax services\nSetting up tax payments and bills for your community, etc.\nAttendance at the annual general meeting of the community\n\nWe can recommend local builders, gardeners, pool maintenance companies, car rental and mechanics.\n\nTranslation services if necessary."],
            ],
            [
                'type' => 'sample_section',
                'heading' => ['en' => 'This small studio may have limited space, but for us it has the heart of a large home. We care for every corner with love, dedication and genuine passion.'],
                'videos' => [
                    [
                        'src' => 'https://video.wixstatic.com/video/c50f24_ce1375bcb2b949b09412a87b1e5990f8/480p/mp4/file.mp4',
                        'poster' => 'https://static.wixstatic.com/media/c50f24_ce1375bcb2b949b09412a87b1e5990f8f000.jpg/v1/fill/w_478,h_850,al_c,q_85,enc_avif,quality_auto/c50f24_ce1375bcb2b949b09412a87b1e5990f8f000.jpg',
                    ],
                    [
                        'src' => 'https://video.wixstatic.com/video/c50f24_4aefdc64ca704e2a9cc3803271e5d4a2/480p/mp4/file.mp4',
                        'poster' => 'https://static.wixstatic.com/media/c50f24_4aefdc64ca704e2a9cc3803271e5d4a2f000.jpg/v1/fill/w_478,h_850,al_c,q_85,enc_avif,quality_auto/c50f24_4aefdc64ca704e2a9cc3803271e5d4a2f000.jpg',
                    ],
                    [
                        'src' => 'https://video.wixstatic.com/video/c50f24_c9d53a87fb2048c18ff5810b19e86639/480p/mp4/file.mp4',
                        'poster' => 'https://static.wixstatic.com/media/c50f24_c9d53a87fb2048c18ff5810b19e86639f000.jpg/v1/fill/w_478,h_850,al_c,q_85,enc_avif,quality_auto/c50f24_c9d53a87fb2048c18ff5810b19e86639f000.jpg',
                    ],
                ],
                'body' => ['en' => "Here is a small sample of how we prepare, present and care for the properties we manage.\nFrom impeccable cleaning to perfect staging, every detail is treated with professionalism and passion, so that your guests always arrive at a welcoming, fresh and unforgettable home."],
            ],
            [
                'type' => 'slider',
                'slides' => [
                    ['title' => 'Master bedroom', 'image' => 'https://static.wixstatic.com/media/c50f24_2924771e373643cfa61cc6cd2a86bfd8~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_2924771e373643cfa61cc6cd2a86bfd8~mv2.jpeg'],
                    ['title' => 'Living room', 'image' => 'https://static.wixstatic.com/media/c50f24_295c6e35052b4142ba6c2facd83c0c54~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_295c6e35052b4142ba6c2facd83c0c54~mv2.jpeg'],
                    ['title' => 'Kitchen', 'image' => 'https://static.wixstatic.com/media/c50f24_c89e08ab621c45ec9f142db0e04672a4~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_c89e08ab621c45ec9f142db0e04672a4~mv2.jpeg'],
                    ['title' => 'Second bedroom', 'image' => 'https://static.wixstatic.com/media/c50f24_3506bf52c47841fb9fdb909e70d5c270~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_3506bf52c47841fb9fdb909e70d5c270~mv2.jpeg'],
                    ['title' => 'Living room', 'image' => 'https://static.wixstatic.com/media/c50f24_b1a074ade9f74ee5aa53d90015ce0634~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_b1a074ade9f74ee5aa53d90015ce0634~mv2.jpeg'],
                    ['title' => 'Kitchen', 'image' => 'https://static.wixstatic.com/media/c50f24_027dbb8861e84d539fc2c60c97fd6cb4~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_027dbb8861e84d539fc2c60c97fd6cb4~mv2.jpeg'],
                    ['title' => 'Master bathroom', 'image' => 'https://static.wixstatic.com/media/c50f24_7f888a67c8fd453da3d1cf5f6ecd2585~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_7f888a67c8fd453da3d1cf5f6ecd2585~mv2.jpeg'],
                    ['title' => 'Extra bathroom', 'image' => 'https://static.wixstatic.com/media/c50f24_a673980e117b411c8cc834683958824c~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_a673980e117b411c8cc834683958824c~mv2.jpeg'],
                    ['title' => 'Guest room', 'image' => 'https://static.wixstatic.com/media/c50f24_26037b3df5f94e5fbbd57ead5fc6a72b~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_26037b3df5f94e5fbbd57ead5fc6a72b~mv2.jpeg'],
                    ['title' => 'Stairs to Roof Terrasse', 'image' => 'https://static.wixstatic.com/media/c50f24_119f2251ae7748f590f05e0fc67fa899~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_119f2251ae7748f590f05e0fc67fa899~mv2.jpeg'],
                    ['title' => 'Roof Terasse', 'image' => 'https://static.wixstatic.com/media/c50f24_f12fd6df9da84bef840605df49aa43d8~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_f12fd6df9da84bef840605df49aa43d8~mv2.jpeg'],
                    ['title' => 'Roof Terrasse', 'image' => 'https://static.wixstatic.com/media/c50f24_14d819a5165348378243f3169343dd33~mv2.jpeg/v1/fill/w_980,h_300,al_c,q_85,enc_avif,quality_auto/c50f24_14d819a5165348378243f3169343dd33~mv2.jpeg'],
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }

    private function touristRentalCategoryBlocks(): array
    {
        return [
            [
                'type' => 'category_products',
                'heading' => ['en' => 'Santana Prime', 'es' => 'Santana Prime'],
                'more_label' => ['en' => 'Mas informacion ....', 'es' => 'Mas informacion ....'],
                'products' => [
                    [
                        'name' => 'Key ownership - monthly check and monitoring',
                        'price' => '€250.00',
                        'sale_price' => '€225.00',
                        'image' => 'https://static.wixstatic.com/media/11062b_aaa3d7aa68764820a4aa29af761f235e~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Key%20Lock.jpg',
                    ],
                    [
                        'name' => 'Key ownership - biweekly control',
                        'price' => '€375.00',
                        'sale_price' => '€337.50',
                        'image' => 'https://static.wixstatic.com/media/11062b_aaa3d7aa68764820a4aa29af761f235e~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Key%20Lock.jpg',
                    ],
                    [
                        'name' => 'Key ownership - weekly check',
                        'price' => '€575.00',
                        'sale_price' => '€517.50',
                        'image' => 'https://static.wixstatic.com/media/11062b_aaa3d7aa68764820a4aa29af761f235e~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Key%20Lock.jpg',
                    ],
                    [
                        'name' => 'Cleaning of commercial premises',
                        'price' => '€0.00',
                        'image' => 'https://static.wixstatic.com/media/c50f24_0ccb27a5b3bb409a8955266883af6aeb~mv2.png/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Office_clean.png',
                    ],
                    [
                        'name' => 'Reception of guests in Torrevieja (other than post codes 03182).',
                        'price' => '€20.00',
                        'image' => 'https://static.wixstatic.com/media/11062b_efb3adc854344396b2b2b4ddc9ef7f69~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Handing%20Over%20Keys.jpg',
                    ],
                    [
                        'name' => 'Reception of guests (only post code 03182) in Torrevieja',
                        'price' => '€15.00',
                        'image' => 'https://static.wixstatic.com/media/11062b_efb3adc854344396b2b2b4ddc9ef7f69~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Handing%20Over%20Keys.jpg',
                    ],
                    [
                        'name' => 'Laundry service',
                        'price' => '€15.00',
                        'image' => 'https://static.wixstatic.com/media/11062b_84511c3f4d9a4ab4b699c4087b28618b~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Hotel%20Laundry%20Room.jpg',
                    ],
                    [
                        'name' => 'Laundry service including rental of towels and bed linen',
                        'price' => '€25.00',
                        'image' => 'https://static.wixstatic.com/media/11062b_84511c3f4d9a4ab4b699c4087b28618b~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Hotel%20Laundry%20Room.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 2 bedrooms and 1 bathroom apartment',
                        'price' => '€54.00',
                        'image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 2 bedrooms and 2 bathrooms apartment',
                        'price' => '€60.00',
                        'image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 3 bedrooms and 1 bathroom apartment',
                        'price' => '€70.00',
                        'image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 3 bedrooms and 2 bathrooms apartment',
                        'price' => '€75.00',
                        'image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 1 bedroom and 1 bathroom apartment',
                        'price' => '€46.00',
                        'image' => 'https://static.wixstatic.com/media/11062b_e59bd196cba4410c9e6066e800d74d6d~mv2_d_3456_5184_s_4_2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Modern%20Living%20Room.jpg',
                    ],
                    [
                        'name' => 'Studio apartment cleaning service',
                        'price' => '€40.00',
                        'image' => 'https://static.wixstatic.com/media/91493d838ac447d19ede2426f503cc8d.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Modern%20Bedroom%20Interior.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 2 bedroom house',
                        'price' => '€65.00',
                        'image' => 'https://static.wixstatic.com/media/feb654e11d3a49daa79c16b483bee805.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/feb654e11d3a49daa79c16b483bee805.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 3 bedroom house',
                        'price' => '€70.00',
                        'image' => 'https://static.wixstatic.com/media/feb654e11d3a49daa79c16b483bee805.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/feb654e11d3a49daa79c16b483bee805.jpg',
                    ],
                    [
                        'name' => 'Cleaning service for private homes',
                        'price' => '€15.00',
                        'image' => 'https://static.wixstatic.com/media/c50f24_0ccb27a5b3bb409a8955266883af6aeb~mv2.png/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Office_clean.png',
                    ],
                    [
                        'name' => 'Cleaning, laundry and key delivery service for studio',
                        'price' => '€60.00',
                        'sale_price' => '€54.00',
                        'image' => 'https://static.wixstatic.com/media/5fd4767141624c128a6f2fe9df273f7f.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Vintage%20Laundry%20Machines.jpg',
                    ],
                    [
                        'name' => 'Cleaning, laundry and key delivery service for 2 bedroom, 2 bathroom apartment',
                        'price' => '€98.00',
                        'sale_price' => '€88.20',
                        'image' => 'https://static.wixstatic.com/media/11062b_84511c3f4d9a4ab4b699c4087b28618b~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Hotel%20Laundry%20Room.jpg',
                    ],
                    [
                        'name' => 'Cleaning, laundry and key delivery service for 2 bedroom, 1 bathroom apartment',
                        'price' => '€90.00',
                        'sale_price' => '€81.00',
                        'image' => 'https://static.wixstatic.com/media/11062b_efb3adc854344396b2b2b4ddc9ef7f69~mv2.jpg/v1/fill/w_360,h_300,al_c,q_85,enc_avif,quality_auto/Handing%20Over%20Keys.jpg',
                    ],
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "La limpieza no es solo lo que hacemos: es lo que somos.\nPasion, precision y profesionalidad en cada detalle.", 'es' => "La limpieza no es solo lo que hacemos: es lo que somos.\nPasion, precision y profesionalidad en cada detalle."],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }

    private function privateHomeBlocks(): array
    {
        return [
            [
                'type' => 'text_section',
                'heading' => ['en' => 'Management of your seasonal / second home-Vila'],
            ],
            [
                'type' => 'gallery',
                'images' => [
                    'https://static.wixstatic.com/media/11062b_8e19e54d8a1f45bebdc63312246937f4~mv2.jpg/v1/fill/w_670,h_482,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Gutachter_in.jpg',
                    'https://static.wixstatic.com/media/11062b_0f70e7ee6ee84899b878c1e8a9451ed1~mv2.jpg/v1/fill/w_644,h_482,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Lockers%20with%20keys.jpg',
                    'https://static.wixstatic.com/media/11062b_29de2e7fdd784693a1b3d013aed52473~mv2.jpeg/v1/fill/w_680,h_482,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Postbox.jpeg',
                    'https://static.wixstatic.com/media/7852e112b197410fb7b2ff1f5fb426a3.jpg/v1/fill/w_656,h_482,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Legal%20Research%20and%20Writing.jpg',
                ],
            ],
            [
                'type' => 'text_section',
                'heading' => ['en' => 'Property Management and Trusted Key Holding Service'],
                'body' => ['en' => "Do you own a second home or only live in Torrevieja seasonally? At Santana Prime, we look after your home as if it were our own - offering you peace of mind through professional inspections, secure key holding and detailed reports you can rely on.\n\nWhat We Offer - Comprehensive Property Management\n\n✓ Annual professional cleaning\n✓ Regular inspections every 7, 15 or 30 days\n✓ Checking ventilation and water flow in kitchen and bathrooms\n✓ Checking electrical installations and appliances\n✓ Detailed reports with photos after each visit\n✓ Scanning and forwarding of correspondence\n✓ Optional additional services such as garden and pool maintenance, airport transfers, minor repairs and representation at community meetings"],
            ],
            [
                'type' => 'text_section',
                'heading' => ['en' => 'Secure Key Holding Service'],
                'body' => ['en' => "Our Inspection Routine\n\n- Ventilation and air circulation\n- Water flow and plumbing in the kitchen and bathrooms\n- Electrical and electronic systems\n- General condition of the property and any possible incidents\n- Minor repairs (subject to authorisation)"],
            ],
            [
                'type' => 'text_section',
                'heading' => ['en' => 'Digital Reports and Total Transparency'],
                'body' => ['en' => "We make monitoring your property easy and worry-free. You will be able to:\n\n- View reports and photos from inspections\n- Check the status of your correspondence\n- Communicate directly with our team\n- Request additional services at any time"],
            ],
            [
                'type' => 'text_section',
                'heading' => ['en' => 'Let us take care of your home - hassle-free'],
                'body' => ['en' => "No matter where you are - in Germany, the United Kingdom, the Netherlands, Scandinavia, or anywhere else.\n\nContact us today for a personalised home care plan."],
            ],
            [
                'type' => 'gallery',
                'images' => [
                    'https://static.wixstatic.com/media/11062b_9daba6d3615440418985a1c4f0f05a98~mv2.jpg/v1/fill/w_594,h_458,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Couple%20Relaxing%20Outdoors.jpg',
                    'https://static.wixstatic.com/media/11062b_3bd46dc3a49e45d897b97d07486147ad~mv2.jpeg/v1/fill/w_614,h_478,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Man%20Inspecting%20Entrance.jpeg',
                    'https://static.wixstatic.com/media/11062b_b6a5893b404d4a60825bd5204ab5e5cc~mv2.jpg/v1/fill/w_594,h_468,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Guests%20Meeting%20Host.jpg',
                    'https://static.wixstatic.com/media/4b6a43d859d31fd9ec83c9d534f0bcfa.jpg/v1/fill/w_604,h_478,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Hand%20Holding%20Key.jpg',
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }

    private function holidayRentalBlocks(): array
    {
        return [
            [
                'type' => 'hero_image',
                'image' => 'https://static.wixstatic.com/media/11062b_61aae7f1f0fc4dc2ad4a418f8550a622~mv2.jpg/v1/fill/w_1920,h_535,al_c,q_85,enc_avif,quality_auto/11062b_61aae7f1f0fc4dc2ad4a418f8550a622~mv2.jpg',
            ],
            [
                'type' => 'text_section',
                'class' => 'is-holiday-intro',
                'heading' => ['en' => 'Charming vacation rentals'],
                'body' => ['en' => "Welcome to your ideal retreat in the heart of Torrevja! Our charming short-term rental offers the perfect combination of comfort, convenience and local flavour, ensuring a memorable stay for every guest. You'll find everything you need to relax and unwind in our thoughtfully designed space."],
            ],
            [
                'type' => 'gallery',
                'images' => [
                    'https://static.wixstatic.com/media/c50f24_3021131a84c545efb31a6b32af10a8b1~mv2.webp/v1/fill/w_620,h_474,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/parasailing.webp',
                    'https://static.wixstatic.com/media/c50f24_cb8fc14ee68544908610b79de25975dc~mv2.jpeg/v1/fill/w_564,h_474,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/beach1.jpeg',
                    'https://static.wixstatic.com/media/c50f24_debefa2268ef49dbab18350ae576c060~mv2.jpg/v1/fill/w_590,h_474,al_c,lg_1,q_80,enc_avif,quality_auto/beach3.jpg',
                    'https://static.wixstatic.com/media/c50f24_9d62acba90e54144b4264a0bdb434561~mv2.jpeg/v1/fill/w_341,h_287,al_c,lg_1,q_80,enc_avif,quality_auto/snorkeling.jpeg',
                ],
            ],
            [
                'type' => 'rental_unit',
                'heading' => ['en' => 'Apartment Santana 2-19'],
                'images' => [
                    'https://static.wixstatic.com/media/c50f24_54f3b656a6454182b532d999f8cbd0f0~mv2.jpg/v1/fill/w_464,h_388,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0007.jpg',
                    'https://static.wixstatic.com/media/c50f24_83fe3f9619c047af8fe216226fded395~mv2.jpg/v1/fill/w_414,h_388,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0008.jpg',
                    'https://static.wixstatic.com/media/c50f24_ec0d39e1f7254e4e8b2f940ba5a9e306~mv2.jpg/v1/fill/w_438,h_388,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0011.jpg',
                    'https://static.wixstatic.com/media/c50f24_75c23546f8b148069e75b2aea8766cfa~mv2.jpg/v1/fill/w_452,h_388,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0006.jpg',
                    'https://static.wixstatic.com/media/c50f24_3f91f9cc09cb48638312f703111c8d76~mv2.jpg/v1/fill/w_476,h_398,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0010.jpg',
                    'https://static.wixstatic.com/media/c50f24_c416533944484463b17538a14b002188~mv2.jpg/v1/fill/w_476,h_388,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0012.jpg',
                    'https://static.wixstatic.com/media/c50f24_58e324c9043b40a0849eea39f2b948a0~mv2.jpg/v1/fill/w_414,h_388,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0017.jpg',
                    'https://static.wixstatic.com/media/c50f24_92af014bf5ac47b08da5c89138252755~mv2.jpg/v1/fill/w_438,h_388,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240112-WA0003.jpg',
                ],
                'body' => ['en' => "Welcome to your perfect getaway! Our stylish one-bedroom rental offers a serene retreat with a beautiful, large pool as its centerpiece. Inside, you'll find a modern living room with an open kitchen, a luxurious bathroom, and a private balcony with stunning pool views.\n\nEnjoy the convenience of private parking, an elevator and access to a communal terrace.\n\nEnjoy ultimate comfort in our holiday home, equipped with all the modern amenities you need. Relax with a smart TV and Bluetooth music system for your entertainment. Stay cool with air conditioning in the summer and cosy with heating in the winter. Our fully furnished kitchen allows you to prepare meals with ease, making your stay truly comfortable and convenient.\n\nWhether you're here to relax or explore, our rentals promise comfort, convenience and a touch of luxury.\n\nBook your stay today and experience the best in short-term accommodation!"],
                'actions' => [
                    ['label' => 'Book online', 'url' => 'https://www.holasantana.com/home'],
                ],
            ],
            [
                'type' => 'wide_image',
                'image' => 'https://static.wixstatic.com/media/c50f24_cf89139e96a942498186504d7f505b64~mv2.jpg/v1/fill/w_1096,h_388,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/swimming_pool_good_2.jpg',
            ],
            [
                'type' => 'rental_unit',
                'heading' => ['en' => 'Studio Apartment Santana 2-18'],
                'images' => [
                    'https://static.wixstatic.com/media/c50f24_08df06e85d024d738e89466c01ff46ec~mv2.jpg/v1/fill/w_456,h_276,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/1.jpg',
                    'https://static.wixstatic.com/media/c50f24_8654e3ac587f4ebfa16693cc1efa9b63~mv2.jpg/v1/fill/w_456,h_314,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/1716217128771.jpg',
                    'https://static.wixstatic.com/media/c50f24_1fd43597c31a47bea886f2c5148095c6~mv2.jpg/v1/fill/w_456,h_372,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0048.jpg',
                    'https://static.wixstatic.com/media/c50f24_20d0d280ff654d4889613445c7a7244d~mv2.jpg/v1/fill/w_426,h_276,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/3.jpg',
                    'https://static.wixstatic.com/media/c50f24_21a2b274f6904496b22c90ca641330c9~mv2.jpg/v1/fill/w_426,h_314,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/1716217128750.jpg',
                    'https://static.wixstatic.com/media/c50f24_83e231717ce945d3a856040dfe742337~mv2.jpg/v1/fill/w_426,h_372,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0038.jpg',
                    'https://static.wixstatic.com/media/c50f24_fcafef9cbac9479d8c7ac57cacade18d~mv2.jpg/v1/fill/w_412,h_276,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/2.jpg',
                    'https://static.wixstatic.com/media/c50f24_8eb9bb7b64b1466480d2f562e24d9110~mv2.jpg/v1/fill/w_412,h_314,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0041.jpg',
                ],
                'body' => ['en' => "Welcome to your perfect getaway! Our stylish studio rental offers a serene retreat with a beautiful, compact pool as its centrepiece. Inside, you'll find a modern living area with an open kitchen, a luxurious bathroom and a private balcony with stunning pool views. All-day sunlight on the balcony is guaranteed.\n\nEnjoy the convenience of private parking, an elevator and access to a communal terrace.\n\nEnjoy ultimate comfort in our holiday home, equipped with all the modern amenities you need. Relax with a smart TV and Bluetooth music system for your entertainment. Stay cool with air conditioning in the summer and cosy with heating in the winter. Our fully furnished kitchen allows you to prepare meals with ease, making your stay truly comfortable and convenient."],
                'actions' => [
                    ['label' => 'Book online', 'url' => 'https://www.holasantana.com/home'],
                ],
            ],
            [
                'type' => 'rental_unit',
                'heading' => ['en' => 'Studio Apartment Santana 3-05'],
                'images' => [
                    'https://static.wixstatic.com/media/c50f24_4fd97b5f913d4634874317d6801c9530~mv2.jpg/v1/fill/w_412,h_372,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/14.jpg',
                    'https://static.wixstatic.com/media/c50f24_77e8fefbc7bf45c3a4544de8c9b37dad~mv2.jpg/v1/fill/w_390,h_276,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0035.jpg',
                    'https://static.wixstatic.com/media/c50f24_495c87385fff439a97bc8c7b6773f9e3~mv2.jpg/v1/fill/w_390,h_314,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0042.jpg',
                    'https://static.wixstatic.com/media/c50f24_f729f64fa73046008b834f4111966792~mv2.jpg/v1/fill/w_390,h_372,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/30.jpg',
                    'https://static.wixstatic.com/media/c50f24_02fa4b8caef0400ba2d43d38da555334~mv2.jpg/v1/fill/w_462,h_360,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/1716215986746.jpg',
                    'https://static.wixstatic.com/media/c50f24_b46e79d606114c799ffa684356c911b3~mv2.jpg/v1/fill/w_462,h_432,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0021.jpg',
                    'https://static.wixstatic.com/media/c50f24_1c606e4284a04a7ca0be881ffadaec89~mv2.jpg/v1/fill/w_462,h_360,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0024.jpg',
                    'https://static.wixstatic.com/media/c50f24_7d2e25728b064df78655784b9f0f6cd0~mv2.jpg/v1/fill/w_462,h_432,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/IMG-20240412-WA0047.jpg',
                ],
                'body' => ['en' => "Welcome to your perfect place! Our chic studio for rent offers a serene retreat with a beautiful, compact pool as its centerpiece. Inside, you'll find a modern living area with an open kitchen, a luxurious bathroom, and a private balcony with stunning pool views.\n\nEnjoy the convenience of private parking, an elevator and access to a communal terrace.\n\nEnjoy ultimate comfort in our holiday home, equipped with all the modern amenities you need. Relax with a smart TV and Bluetooth music system for your entertainment. Stay cool with air conditioning in the summer and cosy with heating in the winter. Our fully furnished kitchen allows you to prepare meals with ease, making your stay truly comfortable and convenient.\n\nWhether you're here to relax or explore, our rentals promise comfort, convenience and a touch of luxury.\n\nBook your stay today and experience the best in short-term accommodation!"],
                'actions' => [
                    ['label' => 'Book online', 'url' => 'https://www.holasantana.com/home'],
                ],
            ],
            [
                'type' => 'rental_unit',
                'heading' => ['en' => 'Apartment Santana Salinas'],
                'images' => [
                    'https://static.wixstatic.com/media/c50f24_2ca06924b7fa41f3ad973523fb9e1b42~mv2.jpeg/v1/fill/w_538,h_396,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/WhatsApp%20Image%202026-04-29%20at%2012_40_11.jpeg',
                    'https://static.wixstatic.com/media/c50f24_aad2756bf8f74dcf9021b36c2f0c36ba~mv2.jpg/v1/fill/w_538,h_396,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/22.jpg',
                    'https://static.wixstatic.com/media/c50f24_bc6e49c7ae714ec3ad03a544f7be08c2~mv2.jpg/v1/fill/w_538,h_396,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/21.jpg',
                    'https://static.wixstatic.com/media/c50f24_6e0c12f8758e464cba04b12e410793f2~mv2.jpg/v1/fill/w_538,h_396,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/2.jpg',
                    'https://static.wixstatic.com/media/c50f24_b0781383f10b46e188cea6f40c577fbe~mv2.jpg/v1/fill/w_538,h_356,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/14.jpg',
                    'https://static.wixstatic.com/media/c50f24_38c73c4b265c4709b2ee0e92ed2be109~mv2.jpg/v1/fill/w_538,h_396,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/19.jpg',
                    'https://static.wixstatic.com/media/c50f24_c193e8cb17b64803aa6adc2e53b6ab46~mv2.jpeg/v1/fill/w_538,h_396,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/WhatsApp%20Image%202025-12-15%20at%2019_24_48.jpeg',
                    'https://static.wixstatic.com/media/c50f24_e9455b60573f49f5a0669da29dc313d9~mv2.jpg/v1/fill/w_538,h_356,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/4.jpg',
                ],
                'body' => ['en' => "Welcome to Santana Salinas! Our stylish two bedroom flat for rent offers a serene view of the Mediterranean Sea from a beautiful stunning balcony. Inside, you will find a modern living room with open kitchen and a luxurious bathroom.\n\nEnjoy the convenience of a lift and access to a communal terrace.\nEnjoy maximum comfort in our holiday home, equipped with all the modern conveniences you need. Relax with a smart TV and Bluetooth music system for your entertainment. Keep cool with air conditioning in summer and cosy with heating in winter. Our fully furnished kitchen allows you to prepare meals with ease, making your stay truly comfortable and convenient.\n\nWhether you're here to relax or explore, our rental promises comfort, convenience and a touch of luxury.\nBook your stay today and enjoy the best in short-term accommodation.\n\nContact us for reservation"],
                'actions' => [
                    ['label' => 'Contact us for reservation', 'url' => 'https://api.whatsapp.com/send?phone=34624229511', 'variant' => 'text'],
                    ['label' => 'Book online', 'url' => 'https://www.holasantana.com/home'],
                ],
            ],
            [
                'type' => 'wide_image',
                'image' => 'https://static.wixstatic.com/media/05e3dc_313e242f412c4998a000fabdbbee8f10.jpg/v1/fill/w_979,h_273,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/05e3dc_313e242f412c4998a000fabdbbee8f10.jpg',
            ],
            [
                'type' => 'text_section',
                'class' => 'is-city-tour',
                'body' => ['en' => "Our holiday home is conveniently located in the heart of Torrevieja, offering easy access to the city's vibrant attractions, pristine beaches and diverse dining options. Immerse yourself in the beauty of Torrevieja, explore its charming streets and enjoy the lively atmosphere of this coastal gem."],
                'actions' => [
                    ['label' => 'Torrevieja city tour', 'url' => 'https://www.youtube.com/watch?v=eB1EYzm4sWY', 'variant' => 'link'],
                    ['label' => 'Torrevieja Portal', 'url' => 'https://www.torrevieja.com/', 'variant' => 'green'],
                    ['label' => 'Torrevieja Coast', 'url' => 'https://turismodetorrevieja.com/', 'variant' => 'green'],
                ],
            ],
            [
                'type' => 'media_text',
                'heading' => ['en' => 'The Santana Family, your host'],
                'body' => ['en' => "Greetings, we are the Santana family and we are proud to share our beloved holiday home with guests looking for a memorable stay in Torrevieja.\n\nWith a passion for hospitality and a deep love for our local community, we are committed to ensuring that every guest experiences the warmth and authenticity of our beloved destination."],
                'image' => 'https://static.wixstatic.com/media/c50f24_67635c3970e64b8385c0cf1ceda30cda~mv2.jpg/v1/fill/w_624,h_882,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/santana_poster_en.jpg',
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }

    private function aboutBlocks(): array
    {
        return [
            [
                'type' => 'about_intro',
                'heading' => ['en' => 'About Santana Prime - Home Care and Tourist Rental Management Service'],
                'body' => ['en' => "Santana Prime provides professional home management and holiday rental services in Torrevieja, including cleaning, laundry, key holding, and full property care. We ensure a smooth, reliable, and worry-free experience for property owners and guests.\n\nOur journey began in 2019 in Germany, where we successfully delivered hospitality services for two years before expanding to Torrevieja, Spain. Drawing on international hotel management experience and hands-on property ownership, we understand the real needs of holiday rentals.\n\nAt Santana Prime, we combine personalised service with professional standards, clear communication, and consistent quality - so your property is always in safe hands."],
            ],
            [
                'type' => 'about_feature',
                'heading' => ['en' => 'Mission:'],
                'body' => ['en' => "We want to change the way you think of Holiday Homes.\n\nOur passion lies in creating vacation home experiences that benefit both property owners and our visitors.\n\nWe strive to make every house a welcoming and practical haven without compromise, believing that your holiday home should stand as the epitome of excellence to attract the most discerning guests."],
                'image' => 'https://static.wixstatic.com/media/11062b_54593c84d61b4570b5a5b260c16ca008~mv2.jpg/v1/fill/w_976,h_1062,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/11062b_54593c84d61b4570b5a5b260c16ca008~mv2.jpg',
            ],
            [
                'type' => 'about_feature',
                'heading' => ['en' => 'Vision:'],
                'body' => ['en' => "We understand the challenges of managing holiday rentals and the importance of effective communication, which is why we offer no-obligation consultations.\n\nTo create cleaner, healthier, and more sustainable spaces by delivering top-quality cleaning services with professionalism, innovation, and eco-friendly solutions."],
                'image' => 'https://static.wixstatic.com/media/11062b_068b7e6d3cad4283833b28adc03699ef~mv2.jpeg/v1/fill/w_978,h_386,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/11062b_068b7e6d3cad4283833b28adc03699ef~mv2.jpeg',
                'reverse' => true,
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }

    private function contactBlocks(): array
    {
        return [
            [
                'type' => 'contact_page',
                'poster' => 'https://static.wixstatic.com/media/c50f24_adf8b55141e04bc48ca2b38dec79393a~mv2.png/v1/fill/w_361,h_303,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Santana_ES.png',
                'office_images' => [
                    'https://static.wixstatic.com/media/c50f24_5ab055d9f846403bb1cdad19ecc9d4bc~mv2.jpg/v1/fill/w_163,h_215,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/1766580327050.jpg',
                    'https://static.wixstatic.com/media/c50f24_5ab055d9f846403bb1cdad19ecc9d4bc~mv2.jpg/v1/fill/w_163,h_215,fp_0.65_0.55,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/1766580327050.jpg',
                ],
                'form_heading' => ['en' => 'Contact us'],
                'form_intro' => ['en' => 'Do you have questions?'],
                'location_heading' => ['en' => 'Finding ourselves'],
                'location_body' => ['en' => 'Our office in Torrevieja, Spain is conveniently located close to the main road, making it easily accessible. If you need specific directions, please feel free to contact us.'],
                'address' => ['Calle Ulpiano 71, Ground floor', '03182 Torrevieja, Spain', 'Tel. +34 601 55 86 27', 'Email: spm3182@gmail.com'],
                'map_url' => 'https://www.google.com/maps/search/?api=1&query=Calle+Ulpiano+71+Torrevieja+Spain',
                'map_label' => ['en' => 'Map'],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }

    private function blogBlocks(): array
    {
        return [
            [
                'type' => 'blog_listing',
                'filter_label' => ['en' => 'All Posts', 'es' => 'All Posts'],
                'heading' => ['en' => 'All Posts', 'es' => 'All Posts'],
                'side_image' => 'https://static.wixstatic.com/media/11062b_4b7c9a8e48334d5aad2fd274fddba3bc~mv2.jpg/v1/fill/w_760,h_1080,al_c,q_80,usm_0.66_1.00_0.01,blur_2,enc_avif,quality_auto/11062b_4b7c9a8e48334d5aad2fd274fddba3bc~mv2.jpg',
                'posts' => [
                    [
                        'author' => 'Santana Prime',
                        'date' => 'May 10',
                        'read_time' => '2 min read',
                        'title' => 'Cleaning Services in Torrevieja: What You Need to Know',
                        'excerpt' => 'Keeping your holiday home in Torrevieja spotless is essential to getting the most out of it. Cleaning not only improves the atmosphere, but also protects the...',
                        'image' => 'https://static.wixstatic.com/media/c50f24_edc8d45c0fe040bf92882e427f1fbcf2~mv2.png/v1/fill/w_1022,h_768,fp_0.50_0.50,q_95,enc_avif,quality_auto/c50f24_edc8d45c0fe040bf92882e427f1fbcf2~mv2.png',
                        'avatar' => 'https://static.wixstatic.com/media/11062b_ba29744d482846cfb08835f7419128a1~mv2.jpg/v1/fill/w_80,h_80,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/11062b_ba29744d482846cfb08835f7419128a1~mv2.jpg',
                        'views' => '0 views',
                        'comments' => '0 comments',
                    ],
                    [
                        'author' => 'Santana Prime',
                        'date' => 'Aug 2, 2025',
                        'read_time' => '1 min read',
                        'title' => 'More bookings, at better prices and happier guest.',
                        'excerpt' => 'We love your property and we would like to manage it for you. Santana - Torrevieja: Your partner for short-Term property management. At...',
                        'avatar' => 'https://static.wixstatic.com/media/11062b_ba29744d482846cfb08835f7419128a1~mv2.jpg/v1/fill/w_80,h_80,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/11062b_ba29744d482846cfb08835f7419128a1~mv2.jpg',
                        'views' => '5 views',
                        'comments' => '0 comments',
                    ],
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }

    private function homeBlocks(): array
    {
        return [
            [
                'type' => 'hero_image',
                'image' => 'https://static.wixstatic.com/media/99ea595a123e414dba96b7c23df3cb87.jpg/v1/fill/w_1423,h_571,al_c,q_85,enc_avif,quality_auto/Modern%20Family%20Home.jpg',
            ],
            [
                'type' => 'panel',
                'heading' => ['en' => 'Owner of a vacation, seasonal, or long-term rental property in Torrevieja or nearby areas?', 'es' => 'Propietario de una vivienda vacacional, de temporada o larga estancia en Torrevieja o alrededores?'],
                'body' => ['en' => "With Santana Prime, manage your property stress-free. We offer a comprehensive service designed both for vacation rentals and for owners who use their property seasonally and need reliable, ongoing care.\n\nWe operate in Torrevieja, Orihuela Costa, La Mata, Guardamar, Punta Prima, Playa Flamenca, Cabo Roig, Los Altos, and other areas of the southern Costa Blanca."],
                'items' => [
                    'Professional cleaning and property preparation',
                    'Laundry, replacement of bed linens and towels, Restocking',
                    'Reservation management, Guest check-in & support.',
                    'Guest hot-line and support.',
                    'Maintenance, periodic inspections and detailed reports',
                    'Tourist licence assistance',
                    'NRA number registration',
                    'NRA renewal support',
                    'Key holding and property supervision',
                    'Ideal service for seasonal property owners who want their property kept in perfect condition while they are away.',
                ],
                'footer' => ['en' => "Why choose us:\nReliable and consistent\nFast response\nLocal expertise\nQuality guaranteed\n\nContact us today for a personalized quote and discover how we can assist you in Torrevieja and the surrounding areas.\n\nComprehensive management for tourist rentals, villas, offices, and both seasonal and private homes - all in one place."],
            ],
            [
                'type' => 'gallery',
                'images' => [
                    'https://static.wixstatic.com/media/4c34e844a76749b2b6c4ed85ec5f5c44.jpg/v1/fill/w_634,h_474,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Modern%20Luxury%20House.jpg',
                    'https://static.wixstatic.com/media/11062b_27a6e9b32a6d44eca54c007b88e94abe~mv2.jpeg/v1/fill/w_614,h_474,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Building%20Inspection.jpeg',
                    'https://static.wixstatic.com/media/11062b_29de2e7fdd784693a1b3d013aed52473~mv2.jpeg/v1/fill/w_634,h_474,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Postbox.jpeg',
                    'https://static.wixstatic.com/media/11062b_3bd46dc3a49e45d897b97d07486147ad~mv2.jpeg/v1/fill/w_654,h_450,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Man%20Inspecting%20Entrance.jpeg',
                ],
            ],
            [
                'type' => 'panel',
                'heading' => ['en' => 'Private, Seasonal home:', 'es' => 'Vivienda privada o de temporada:'],
                'body' => ['en' => 'We offer you a new perspective on care, the service you deserve, people you trust.'],
                'items' => [
                    'Secure, dedicated key storage for each property',
                    'Regular inspections and monitoring to ensure everything is in order',
                    'Detailed status updates after each visit',
                    'Collection and electronic delivery of incoming mail',
                    'Minor repairs carried out with owner approval',
                    'Representation at community meetings, if required',
                    'Assistance with insurance and tax matters',
                ],
                'actions' => [
                    ['label' => '*Check our offer', 'url' => '/en/category/tourist-rental-service'],
                    ['label' => 'Contact us', 'url' => 'https://api.whatsapp.com/send?phone=34624229511'],
                ],
            ],
            [
                'type' => 'gallery',
                'images' => [
                    'https://static.wixstatic.com/media/11062b_9fa0a758b5c74abe8b22d362456644ee~mv2.jpg/v1/fill/w_546,h_504,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Cleaning%20Team%20Portrait.jpg',
                    'https://static.wixstatic.com/media/11062b_068b7e6d3cad4283833b28adc03699ef~mv2.jpeg/v1/fill/w_456,h_504,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Cleaning%20the%20Windows.jpeg',
                    'https://static.wixstatic.com/media/11062b_29ddc6a8cb6d4007a2de61d8ce8f9d58~mv2.jpg/v1/fill/w_488,h_504,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Bathroom%20Cleaner.jpg',
                    'https://static.wixstatic.com/media/11062b_84511c3f4d9a4ab4b699c4087b28618b~mv2.jpg/v1/fill/w_546,h_504,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Hotel%20Laundry%20Room.jpg',
                    'https://static.wixstatic.com/media/11062b_efb3adc854344396b2b2b4ddc9ef7f69~mv2.jpg/v1/fill/w_546,h_504,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Handing%20Over%20Keys.jpg',
                ],
            ],
            [
                'type' => 'split',
                'heading' => ['en' => 'Holiday rental management:', 'es' => 'Gestion de alquiler vacacional:'],
                'body' => ['en' => "SANTANA is a specialist in tourist rental management, focusing on short-term rentals and seasonal homes.\n\nWe are passionate about creating exceptional holiday home experiences that delight both owners and guests.\n\nOur goal is to transform the way you experience holiday homes - combining comfort, quality, and convenience.\n\nOur mission is to redefine holiday home management by delivering the highest standards at fair and transparent prices."],
                'items' => [
                    'Digital property management for full control and transparency',
                    'Easy tracking of all activities and updates',
                    'Professional cleaning services',
                    'Laundry services, including in-house workshop support',
                    'Reservation management and guest communication',
                    '24/7 guest support and hotline',
                    'Key delivery services and secure Dropbox access',
                    'Representation at community meetings (AGM) if needed',
                    'Assistance with insurance and tax management',
                ],
                'image' => 'https://static.wixstatic.com/media/c50f24_0ccb27a5b3bb409a8955266883af6aeb~mv2.png/v1/fill/w_499,h_788,al_c,q_90,enc_avif,quality_auto/Office_clean.png',
                'actions' => [
                    ['label' => '*Check our offer', 'url' => '/en/category/tourist-rental-service'],
                    ['label' => 'Contact us', 'url' => 'https://api.whatsapp.com/send?phone=34624229511'],
                ],
            ],
            [
                'type' => 'gallery',
                'images' => [
                    'https://static.wixstatic.com/media/11062b_41561a16bd94495d8c9c685b35a1f468~mv2.jpg/v1/fill/w_546,h_546,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Putzfrauen.jpg',
                    'https://static.wixstatic.com/media/11062b_61618c092a3e452aac5e8a3a87f9e332~mv2.jpg/v1/fill/w_626,h_546,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Cleaning%20with%20a%20Mop.jpg',
                    'https://static.wixstatic.com/media/11062b_ad421945ebe44142934f333e6725ae51~mv2.jpeg/v1/fill/w_742,h_530,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Basket%20Of%20Linens.jpeg',
                    'https://static.wixstatic.com/media/11062b_cb9d627b8f344dbe9b6c3da26e50317b~mv2.jpg/v1/fill/w_636,h_530,al_c,q_80,usm_0.66_1.00_0.01,enc_avif,quality_auto/Ein%20Bett%20machen.jpg',
                ],
            ],
            [
                'type' => 'panel',
                'heading' => ['en' => "Cleaning of Holiday Home, AIRBNB's", 'es' => 'Limpieza de viviendas vacacionales y AIRBNB'],
                'body' => ['en' => "At Santana Prime, we understand that first impressions matter - especially when welcoming guests to your holiday property. Our Holiday Rental Cleaning Service is designed to keep your property spotless, fresh, and perfectly prepared for every new arrival.\n\nWe handle everything with attention to detail - from deep cleaning the kitchen and bathrooms to changing bed linens, washing towels, and ensuring every corner shines. Our team also checks essentials such as lighting, appliances, and amenities so your guests enjoy a flawless stay from the moment they walk in.\n\nWhether you manage one apartment or several properties, our flexible scheduling and quick turnaround service guarantee that your rentals are always ready on time. With Santana Prime, you can relax knowing your guests will always find a home that's clean, comfortable, and welcoming."],
                'items' => [
                    'Complete pre- and post-guest cleaning',
                    'Bed linen and towel replacement',
                    'Restocking basic amenities (upon request)',
                    'Detailed final inspection before guest check-in',
                ],
            ],
            [
                'type' => 'panel',
                'heading' => ['en' => 'Cleaning of private, seasonal home/Villa and office:', 'es' => 'Limpieza de vivienda privada, de temporada, villa y oficina:'],
                'body' => ['en' => "Keeping your home clean and organized shouldn't be a challenge. Our House Cleaning Service is designed to make your everyday life easier. Whether you need a regular weekly cleaning or a deep seasonal refresh, our experienced team provides reliable, professional, and personalized care for your home.\n\nWe use safe and effective cleaning products, giving attention to every detail - floors, bathrooms, kitchens, windows, and more - so you can come home to a spotless and relaxing space."],
                'items' => [
                    'Regular or one-time cleaning options',
                    'Eco-friendly cleaning products (on request)',
                    'Flexible scheduling to suit your lifestyle',
                    'Trusted, insured, and trained professionals',
                ],
                'footer' => ['en' => "Environmentally friendly and allergen-free cleaning options.\nTop quality, fair price.\nGuarantee on quality of service.\nNo hidden cost.\n\nFor exclusive offers and personalised discounts, contact us today."],
                'actions' => [
                    ['label' => 'Contact us', 'url' => 'https://api.whatsapp.com/send?phone=34624229511'],
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => 'https://static.wixstatic.com/media/c50f24_2bee8acd2b094103820a9c8b22e1f9f1~mv2.jpg/v1/fill/w_784,h_988,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20home%20cleaning%20service.jpg',
                'right_image' => 'https://static.wixstatic.com/media/c50f24_b7220545dfd7477b8eea148404e68422~mv2.jpg/v1/fill/w_774,h_968,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/a%20vertical%20image%20of%20a%20home%20cleaning%20service%20.jpg',
            ],
        ];
    }
}
