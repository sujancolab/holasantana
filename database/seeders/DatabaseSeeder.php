<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Language;
use App\Models\HolidayHome;
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

        foreach ([
            ['code' => 'en', 'name' => 'English', 'is_default' => true, 'sort_order' => 1],
            ['code' => 'es', 'name' => 'Spanish', 'is_default' => false, 'sort_order' => 2],
            ['code' => 'de', 'name' => 'German', 'is_default' => false, 'sort_order' => 3],
            ['code' => 'sv', 'name' => 'Swedish', 'is_default' => false, 'sort_order' => 4],
            ['code' => 'fi', 'name' => 'Finnish', 'is_default' => false, 'sort_order' => 5],
        ] as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                [
                    'name' => $language['name'],
                    'is_default' => $language['is_default'],
                    'is_active' => true,
                    'sort_order' => $language['sort_order'],
                ],
            );
        }

        $pages = [
            ['home', 'Welcome', 'Inicio', 'Real estate and holiday rental management in Gran Canaria', 'Property care, guest services, and holiday homes managed with local attention.', '/'],
            ['general-4', 'Management of tourist rental', 'Gestion de alquiler turistico', 'Tourist rental management', 'From listing preparation to guest communication, keep your tourist rental moving smoothly.', '/general-4'],
            ['projects-6', 'Management of private home', 'Gestion de vivienda privada', 'Private home management', 'Reliable support for owners who want their home cared for while they are away.', '/projects-6'],
            ['category/tourist-rental-service', 'Buy our services', 'Comprar nuestro servicio', 'Buy our services', 'Choose the service level that fits your rental property and ownership goals.', '/category/tourist-rental-service'],
            ['home-rental', 'Rent our holiday home', 'Alquila nuestra casa vacacional', 'Rent our holiday home', 'Explore available holiday homes and plan a comfortable stay in the islands.', '/home'],
            ['about-3', 'About', 'Sobre nosotros', 'About Hola Santana', 'A local team helping homeowners and travelers with practical, personal service.', '/about-3'],
            ['contact', 'Contact', 'Contacto', 'Contact Hola Santana', 'Send an enquiry and the team will help with the right next step.', '/contact'],
            ['faq', 'FAQ', 'FAQ', 'Frequently asked questions', 'Find quick answers about Santana Prime property care, cleaning, key holding, and holiday rental management.', '/faq'],
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
            $isFaq = $slug === 'faq';
            $isBlog = $slug === 'blog';

            $heroTitleEn = match (true) {
                $isHome => 'Earn a profit on your property easily and safely',
                $isTouristRental => 'Stress-Free Holiday Rental Management - We handle cleaning, guests, laundry & maintenance',
                $isPrivateHome => 'Management of your seasonal / second home-Vila',
                $isTouristRentalCategory => 'Santana Prime',
                $isHolidayRental => 'Welcome to the Holiday Home Santana',
                $isAbout => 'About Santana Prime - Home Care and Tourist Rental Management Service',
                $isContact => 'Santana Prime - Home care and Tourist rental management services',
                $isFaq => 'Frequently asked questions',
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
                $isFaq => 'Find quick answers about Santana Prime property care, cleaning, key holding, and holiday rental management.',
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
                $isFaq => $this->faqBlocks(),
                $isBlog => $this->blogBlocks(),
                default => $this->defaultBlocks($slug),
            };

            $template = $isHome ? 'home' : (($isTouristRental || $isPrivateHome || $isTouristRentalCategory || $isHolidayRental || $isAbout || $isContact || $isFaq || $isBlog) ? 'prime' : 'default');

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

        $this->seedHolidayHomes();
    }

    private function seedHolidayHomes(): void
    {
        $homes = [
            [
                'area_name' => 'Torrevieja',
                'name' => 'Apartment Santana 2-19',
                'image_url' => '/assets/wix-assets/05cc8d9510c17b4b-img-20240112-wa0007.jpg',
                'description' => 'Stylish one-bedroom rental with pool views, private parking, elevator access and a fully equipped kitchen.',
                'number_of_bedrooms' => 1,
                'maximum_number_of_guests' => 4,
                'online_booking_link' => 'https://www.holasantana.com/home',
                'sort_order' => 10,
            ],
            [
                'area_name' => 'Torrevieja',
                'name' => 'Studio Apartment Santana 2-18',
                'image_url' => '/assets/wix-assets/67335c95154ef5be-1.jpg',
                'description' => 'Bright studio with balcony, compact pool, private parking and modern amenities for a comfortable stay.',
                'number_of_bedrooms' => 0,
                'maximum_number_of_guests' => 2,
                'online_booking_link' => 'https://www.holasantana.com/home',
                'sort_order' => 20,
            ],
            [
                'area_name' => 'Torrevieja',
                'name' => 'Studio Apartment Santana 3-05',
                'image_url' => '/assets/wix-assets/208b79af86e60916-14.jpg',
                'description' => 'Chic studio with pool views, elevator access, furnished kitchen, air conditioning and heating.',
                'number_of_bedrooms' => 0,
                'maximum_number_of_guests' => 2,
                'online_booking_link' => 'https://www.holasantana.com/home',
                'sort_order' => 30,
            ],
            [
                'area_name' => 'Salinas',
                'name' => 'Apartment Santana Salinas',
                'image_url' => '/assets/wix-assets/c34c93919407f737-whatsapp-image-2026-04-29-at-12-40-11.jpeg',
                'description' => 'Two-bedroom apartment with Mediterranean Sea view, balcony, lift access and a modern open kitchen.',
                'number_of_bedrooms' => 2,
                'maximum_number_of_guests' => 4,
                'online_booking_link' => 'https://api.whatsapp.com/send?phone=34624229511',
                'sort_order' => 40,
            ],
        ];

        foreach ($homes as $home) {
            HolidayHome::updateOrCreate(
                ['name' => $home['name']],
                $home + ['is_active' => true],
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
                    '/assets/wix-assets/d3812977f020bd8f-modern-living-room.jpg',
                    '/assets/wix-assets/9355e4deed2bead8-modern-bedroom-interior.jpg',
                    '/assets/wix-assets/898a17de2d375b6a-feb654e11d3a49daa79c16b483bee805.jpg',
                ],
            ],
            [
                'type' => 'open_intro',
                'body' => ['en' => "Santana Prime specializes in the comprehensive management of vacation rentals, offering exceptional service that ensures the satisfaction of each guest. At our company, we use biodegradable and allergen-free products, along with state-of-the-art equipment, all managed by a team of highly trained experts, committed to providing an efficient and environmentally friendly service.\n\nIn addition, we provide laundry services, key delivery and reception of guests for property demonstrations, ensuring a memorable experience for our clients.\n\nWe also offer additional options, such as reservation management and airport transfers, with the aim of optimising visitors' stay as much as possible.\n\nOur aim is to offer a unique service and make our clients' stay as comfortable and pleasant as possible."],
            ],
            [
                'type' => 'text_section',
                'class' => 'is-services-heading',
                'heading' => ['en' => 'All our services:'],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Why choose our cleaning services?'],
                'images' => [
                    '/assets/wix-assets/12726f5bb117f054-office-clean.png',
                    '/assets/wix-assets/7a8db6192a851d73-cleaning-team-portrait.jpg',
                ],
                'body' => ['en' => "Excellence in every detail: Our cleaners pride themselves on their attention to detail, ensuring a level of excellence that exceeds expectations.\n\nTailored to your needs: Our cleaning services are flexible and tailored to your specific needs, whether it's preparing for your guests or maintaining your own space.\n\nTime-saving solutions: Enjoy more free time without compromising on cleaning. Our efficient cleaning services allow you to focus on what matters most.\n\nElevate your living experience with our professional cleaning services. Immerse yourself in a world of cleanliness and relaxation, where every detail is carefully taken care of."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Why choose our check-in and check-out service?'],
                'images' => [
                    '/assets/wix-assets/d3414d6c3165e70b-key-lock.jpg',
                    '/assets/wix-assets/ca81161b41b64d2b-handing-over-keys.jpg',
                ],
                'body' => ['en' => "Peace of mind: Entrust the logistics to our experienced team, allowing you to focus on what matters most.\n\nProfessional Presentation: Make a lasting impression with a well-organized and professional check-in and check-out process.\n\nTime Saving: Streamline your property management tasks and save valuable time with our efficient services."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Laundry service'],
                'images' => [
                    '/assets/wix-assets/a2d219f13b0148f1-vintage-laundry-machines.jpg',
                    '/assets/wix-assets/f1e8c414df3223ca-hotel-laundry-room.jpg',
                ],
                'body' => ['en' => "Effortless laundry: Your bed linen and towels can be washed and ironed at our own laundry station without lifting a finger.\n\nFor just EUR 15 per booking (provided you provide spare sets of sheets and towels), our full laundry service (we deliver sets of sheets and towels) EUR 20 ensures your living spaces remain immaculate and inviting for the next guests."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'General maintenance and repairs'],
                'images' => [
                    '/assets/wix-assets/c1258e6f8e6fcb31-pipe-repair-close-up.jpeg',
                    '/assets/wix-assets/b643e39f91980af8-air-conditioner-maintenance.jpg',
                ],
                'body' => ['en' => "Your comfort is our top priority.\nWe are proud to offer a wide range of maintenance services.\nWe understand that every home is unique.\nWe have your back when it comes to basic home maintenance, preventative checkups, painting, and much more."],
            ],
            [
                'type' => 'service_section',
                'heading' => ['en' => 'Additional service'],
                'images' => [
                    '/assets/wix-assets/a944c6d0c2f0dd5e-signing-a-document.jpg',
                    '/assets/wix-assets/d0600418daa572d2-hand-stamping-document.jpg',
                ],
                'body' => ['en' => "We can also help you with:\n\nObtaining a NIE number and opening a bank account\nNotarial and tax services\nSetting up tax payments and bills for your community, etc.\nAttendance at the annual general meeting of the community\n\nWe can recommend local builders, gardeners, pool maintenance companies, car rental and mechanics.\n\nTranslation services if necessary."],
            ],
            [
                'type' => 'sample_section',
                'heading' => ['en' => 'This small studio may have limited space, but for us it has the heart of a large home. We care for every corner with love, dedication and genuine passion.'],
                'videos' => [
                    [
                        'src' => '/assets/wix-assets/cc254de4827a3a79-file.mp4',
                        'poster' => '/assets/wix-assets/527b99cb5ebf2c3e-c50f24-ce1375bcb2b949b09412a87b1e5990f8f000.jpg',
                    ],
                    [
                        'src' => '/assets/wix-assets/e6f746493e8bc77d-file.mp4',
                        'poster' => '/assets/wix-assets/aa82bf1102fb3194-c50f24-4aefdc64ca704e2a9cc3803271e5d4a2f000.jpg',
                    ],
                    [
                        'src' => '/assets/wix-assets/0b42add7dfa91d28-file.mp4',
                        'poster' => '/assets/wix-assets/2a13252bbe6fefff-c50f24-c9d53a87fb2048c18ff5810b19e86639f000.jpg',
                    ],
                ],
                'body' => ['en' => "Here is a small sample of how we prepare, present and care for the properties we manage.\nFrom impeccable cleaning to perfect staging, every detail is treated with professionalism and passion, so that your guests always arrive at a welcoming, fresh and unforgettable home."],
            ],
            [
                'type' => 'slider',
                'slides' => [
                    ['title' => 'Master bedroom', 'image' => '/assets/wix-assets/a6e7b56402a3816f-c50f24-2924771e373643cfa61cc6cd2a86bfd8-mv2.jpeg'],
                    ['title' => 'Living room', 'image' => '/assets/wix-assets/fb38e9a09f5b0d2b-c50f24-295c6e35052b4142ba6c2facd83c0c54-mv2.jpeg'],
                    ['title' => 'Kitchen', 'image' => '/assets/wix-assets/1f974d73a202a741-c50f24-c89e08ab621c45ec9f142db0e04672a4-mv2.jpeg'],
                    ['title' => 'Second bedroom', 'image' => '/assets/wix-assets/7ab828a52e37a7b5-c50f24-3506bf52c47841fb9fdb909e70d5c270-mv2.jpeg'],
                    ['title' => 'Living room', 'image' => '/assets/wix-assets/6c09a5d2589fe6e8-c50f24-b1a074ade9f74ee5aa53d90015ce0634-mv2.jpeg'],
                    ['title' => 'Kitchen', 'image' => '/assets/wix-assets/8476b1bfe8d371e4-c50f24-027dbb8861e84d539fc2c60c97fd6cb4-mv2.jpeg'],
                    ['title' => 'Master bathroom', 'image' => '/assets/wix-assets/68c183d59f6319ed-c50f24-7f888a67c8fd453da3d1cf5f6ecd2585-mv2.jpeg'],
                    ['title' => 'Extra bathroom', 'image' => '/assets/wix-assets/72b8e5b83dd0efc0-c50f24-a673980e117b411c8cc834683958824c-mv2.jpeg'],
                    ['title' => 'Guest room', 'image' => '/assets/wix-assets/f73bf286ac1c9ac5-c50f24-26037b3df5f94e5fbbd57ead5fc6a72b-mv2.jpeg'],
                    ['title' => 'Stairs to Roof Terrasse', 'image' => '/assets/wix-assets/6cc05b725b2a28ca-c50f24-119f2251ae7748f590f05e0fc67fa899-mv2.jpeg'],
                    ['title' => 'Roof Terasse', 'image' => '/assets/wix-assets/d8c188519c2d4cb8-c50f24-f12fd6df9da84bef840605df49aa43d8-mv2.jpeg'],
                    ['title' => 'Roof Terrasse', 'image' => '/assets/wix-assets/aa189b161323c2af-c50f24-14d819a5165348378243f3169343dd33-mv2.jpeg'],
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
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
                        'image' => '/assets/wix-assets/349946898a11d7ac-key-lock.jpg',
                    ],
                    [
                        'name' => 'Key ownership - biweekly control',
                        'price' => '€375.00',
                        'sale_price' => '€337.50',
                        'image' => '/assets/wix-assets/349946898a11d7ac-key-lock.jpg',
                    ],
                    [
                        'name' => 'Key ownership - weekly check',
                        'price' => '€575.00',
                        'sale_price' => '€517.50',
                        'image' => '/assets/wix-assets/349946898a11d7ac-key-lock.jpg',
                    ],
                    [
                        'name' => 'Cleaning of commercial premises',
                        'price' => '€0.00',
                        'image' => '/assets/wix-assets/59692305f5d6bb83-office-clean.png',
                    ],
                    [
                        'name' => 'Reception of guests in Torrevieja (other than post codes 03182).',
                        'price' => '€20.00',
                        'image' => '/assets/wix-assets/1ad0bdebee35d6a8-handing-over-keys.jpg',
                    ],
                    [
                        'name' => 'Reception of guests (only post code 03182) in Torrevieja',
                        'price' => '€15.00',
                        'image' => '/assets/wix-assets/1ad0bdebee35d6a8-handing-over-keys.jpg',
                    ],
                    [
                        'name' => 'Laundry service',
                        'price' => '€15.00',
                        'image' => '/assets/wix-assets/ce6b2c0b27bc1df8-hotel-laundry-room.jpg',
                    ],
                    [
                        'name' => 'Laundry service including rental of towels and bed linen',
                        'price' => '€25.00',
                        'image' => '/assets/wix-assets/ce6b2c0b27bc1df8-hotel-laundry-room.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 2 bedrooms and 1 bathroom apartment',
                        'price' => '€54.00',
                        'image' => '/assets/wix-assets/3edee5c34634237b-a-vertical-image-of-home-cleaning-service.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 2 bedrooms and 2 bathrooms apartment',
                        'price' => '€60.00',
                        'image' => '/assets/wix-assets/4b7f78f219d80f74-a-vertical-image-of-a-home-cleaning-service.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 3 bedrooms and 1 bathroom apartment',
                        'price' => '€70.00',
                        'image' => '/assets/wix-assets/3edee5c34634237b-a-vertical-image-of-home-cleaning-service.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 3 bedrooms and 2 bathrooms apartment',
                        'price' => '€75.00',
                        'image' => '/assets/wix-assets/4b7f78f219d80f74-a-vertical-image-of-a-home-cleaning-service.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 1 bedroom and 1 bathroom apartment',
                        'price' => '€46.00',
                        'image' => '/assets/wix-assets/ce98faa53920e9f8-modern-living-room.jpg',
                    ],
                    [
                        'name' => 'Studio apartment cleaning service',
                        'price' => '€40.00',
                        'image' => '/assets/wix-assets/1c0fc7e7f93b501e-modern-bedroom-interior.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 2 bedroom house',
                        'price' => '€65.00',
                        'image' => '/assets/wix-assets/71efa38bec3aaa34-feb654e11d3a49daa79c16b483bee805.jpg',
                    ],
                    [
                        'name' => 'Cleaning service of 3 bedroom house',
                        'price' => '€70.00',
                        'image' => '/assets/wix-assets/71efa38bec3aaa34-feb654e11d3a49daa79c16b483bee805.jpg',
                    ],
                    [
                        'name' => 'Cleaning service for private homes',
                        'price' => '€15.00',
                        'image' => '/assets/wix-assets/59692305f5d6bb83-office-clean.png',
                    ],
                    [
                        'name' => 'Cleaning, laundry and key delivery service for studio',
                        'price' => '€60.00',
                        'sale_price' => '€54.00',
                        'image' => '/assets/wix-assets/7c82092db36a58af-vintage-laundry-machines.jpg',
                    ],
                    [
                        'name' => 'Cleaning, laundry and key delivery service for 2 bedroom, 2 bathroom apartment',
                        'price' => '€98.00',
                        'sale_price' => '€88.20',
                        'image' => '/assets/wix-assets/ce6b2c0b27bc1df8-hotel-laundry-room.jpg',
                    ],
                    [
                        'name' => 'Cleaning, laundry and key delivery service for 2 bedroom, 1 bathroom apartment',
                        'price' => '€90.00',
                        'sale_price' => '€81.00',
                        'image' => '/assets/wix-assets/1ad0bdebee35d6a8-handing-over-keys.jpg',
                    ],
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "La limpieza no es solo lo que hacemos: es lo que somos.\nPasion, precision y profesionalidad en cada detalle.", 'es' => "La limpieza no es solo lo que hacemos: es lo que somos.\nPasion, precision y profesionalidad en cada detalle."],
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
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
                    '/assets/wix-assets/142e3b5a9a237839-gutachter-in.jpg',
                    '/assets/wix-assets/25e74731d79718c4-lockers-with-keys.jpg',
                    '/assets/wix-assets/845a5f617e5f5068-postbox.jpeg',
                    '/assets/wix-assets/768b0b11fae7b2dd-legal-research-and-writing.jpg',
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
                    '/assets/wix-assets/086dcc3fb63345e8-couple-relaxing-outdoors.jpg',
                    '/assets/wix-assets/1e0758c60e674d91-man-inspecting-entrance.jpeg',
                    '/assets/wix-assets/805f2a44e012af7d-guests-meeting-host.jpg',
                    '/assets/wix-assets/66d1891a5136a4e8-hand-holding-key.jpg',
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
            ],
        ];
    }

    private function holidayRentalBlocks(): array
    {
        return [
            [
                'type' => 'hero_image',
                'image' => '/assets/wix-assets/9903f3cb2c6ca52a-11062b-61aae7f1f0fc4dc2ad4a418f8550a622-mv2.jpg',
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
                    '/assets/wix-assets/4858f1ecf5bc08fd-parasailing.webp',
                    '/assets/wix-assets/5deb719370d5af0a-beach1.jpeg',
                    '/assets/wix-assets/469a5023df488987-beach3.jpg',
                    '/assets/wix-assets/2ed2072d1498b507-snorkeling.jpeg',
                ],
            ],
            [
                'type' => 'rental_unit',
                'heading' => ['en' => 'Apartment Santana 2-19'],
                'images' => [
                    '/assets/wix-assets/05cc8d9510c17b4b-img-20240112-wa0007.jpg',
                    '/assets/wix-assets/ce159c2a4652691b-img-20240112-wa0008.jpg',
                    '/assets/wix-assets/cd6b783642288db8-img-20240112-wa0011.jpg',
                    '/assets/wix-assets/319be6409bf562e7-img-20240112-wa0006.jpg',
                    '/assets/wix-assets/93de7ee73384f4ed-img-20240112-wa0010.jpg',
                    '/assets/wix-assets/ee53772994b3b80f-img-20240112-wa0012.jpg',
                    '/assets/wix-assets/8172960e606c8528-img-20240112-wa0017.jpg',
                    '/assets/wix-assets/8336e7916a3607af-img-20240112-wa0003.jpg',
                ],
                'body' => ['en' => "Welcome to your perfect getaway! Our stylish one-bedroom rental offers a serene retreat with a beautiful, large pool as its centerpiece. Inside, you'll find a modern living room with an open kitchen, a luxurious bathroom, and a private balcony with stunning pool views.\n\nEnjoy the convenience of private parking, an elevator and access to a communal terrace.\n\nEnjoy ultimate comfort in our holiday home, equipped with all the modern amenities you need. Relax with a smart TV and Bluetooth music system for your entertainment. Stay cool with air conditioning in the summer and cosy with heating in the winter. Our fully furnished kitchen allows you to prepare meals with ease, making your stay truly comfortable and convenient.\n\nWhether you're here to relax or explore, our rentals promise comfort, convenience and a touch of luxury.\n\nBook your stay today and experience the best in short-term accommodation!"],
                'actions' => [
                    ['label' => 'Book online', 'url' => 'https://www.holasantana.com/home'],
                ],
            ],
            [
                'type' => 'wide_image',
                'image' => '/assets/wix-assets/fb96194423b0f3f0-swimming-pool-good-2.jpg',
            ],
            [
                'type' => 'rental_unit',
                'heading' => ['en' => 'Studio Apartment Santana 2-18'],
                'images' => [
                    '/assets/wix-assets/67335c95154ef5be-1.jpg',
                    '/assets/wix-assets/e14693849a9ff2b8-1716217128771.jpg',
                    '/assets/wix-assets/f3e7e02299985c29-img-20240412-wa0048.jpg',
                    '/assets/wix-assets/ba299d6bae7b1bc3-3.jpg',
                    '/assets/wix-assets/0a32197554bc6fac-1716217128750.jpg',
                    '/assets/wix-assets/c7f13cbb276b3f9c-img-20240412-wa0038.jpg',
                    '/assets/wix-assets/f80db035a1d3dba8-2.jpg',
                    '/assets/wix-assets/2c8fcd4d15daf29a-img-20240412-wa0041.jpg',
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
                    '/assets/wix-assets/208b79af86e60916-14.jpg',
                    '/assets/wix-assets/da873dd30b8c7cce-img-20240412-wa0035.jpg',
                    '/assets/wix-assets/5fab718feca8501a-img-20240412-wa0042.jpg',
                    '/assets/wix-assets/b9e0421671c410a0-30.jpg',
                    '/assets/wix-assets/b367dcfa4aea122a-1716215986746.jpg',
                    '/assets/wix-assets/f47bda8dca6f762f-img-20240412-wa0021.jpg',
                    '/assets/wix-assets/0e76f146f7f5536a-img-20240412-wa0024.jpg',
                    '/assets/wix-assets/39bc59723837c443-img-20240412-wa0047.jpg',
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
                    '/assets/wix-assets/c34c93919407f737-whatsapp-image-2026-04-29-at-12-40-11.jpeg',
                    '/assets/wix-assets/d81749485007ca63-22.jpg',
                    '/assets/wix-assets/2a166b7325eb2b3a-21.jpg',
                    '/assets/wix-assets/fe3b52b633cc7761-2.jpg',
                    '/assets/wix-assets/b67c1df70628ec37-14.jpg',
                    '/assets/wix-assets/526c39ae340f3a6f-19.jpg',
                    '/assets/wix-assets/cce4e33c2816fef4-whatsapp-image-2025-12-15-at-19-24-48.jpeg',
                    '/assets/wix-assets/58c72cb86f7d6245-4.jpg',
                ],
                'body' => ['en' => "Welcome to Santana Salinas! Our stylish two bedroom flat for rent offers a serene view of the Mediterranean Sea from a beautiful stunning balcony. Inside, you will find a modern living room with open kitchen and a luxurious bathroom.\n\nEnjoy the convenience of a lift and access to a communal terrace.\nEnjoy maximum comfort in our holiday home, equipped with all the modern conveniences you need. Relax with a smart TV and Bluetooth music system for your entertainment. Keep cool with air conditioning in summer and cosy with heating in winter. Our fully furnished kitchen allows you to prepare meals with ease, making your stay truly comfortable and convenient.\n\nWhether you're here to relax or explore, our rental promises comfort, convenience and a touch of luxury.\nBook your stay today and enjoy the best in short-term accommodation.\n\nContact us for reservation"],
                'actions' => [
                    ['label' => 'Contact us for reservation', 'url' => 'https://api.whatsapp.com/send?phone=34624229511', 'variant' => 'text'],
                    ['label' => 'Book online', 'url' => 'https://www.holasantana.com/home'],
                ],
            ],
            [
                'type' => 'wide_image',
                'image' => '/assets/wix-assets/af0b6c823b851b15-05e3dc-313e242f412c4998a000fabdbbee8f10.jpg',
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
                'image' => '/assets/wix-assets/8e66850528c79162-santana-poster-en.jpg',
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
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
                'image' => '/assets/wix-assets/1d4ec1a9c97efb43-11062b-54593c84d61b4570b5a5b260c16ca008-mv2.jpg',
            ],
            [
                'type' => 'about_feature',
                'heading' => ['en' => 'Vision:'],
                'body' => ['en' => "We understand the challenges of managing holiday rentals and the importance of effective communication, which is why we offer no-obligation consultations.\n\nTo create cleaner, healthier, and more sustainable spaces by delivering top-quality cleaning services with professionalism, innovation, and eco-friendly solutions."],
                'image' => '/assets/wix-assets/54310ef93d16b1bb-11062b-068b7e6d3cad4283833b28adc03699ef-mv2.jpeg',
                'reverse' => true,
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
            ],
        ];
    }

    private function contactBlocks(): array
    {
        return [
            [
                'type' => 'contact_page',
                'poster' => '/assets/wix-assets/751184b17a0fec8f-santana-es.png',
                'office_images' => [
                    '/assets/wix-assets/d7373a1b8fec6713-1766580327050.jpg',
                    '/assets/wix-assets/fa5c6b85d494d42a-1766580327050.jpg',
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
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
            ],
        ];
    }

    private function faqBlocks(): array
    {
        return [
            [
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
                'side_image' => '/assets/wix-assets/da4b49ea81221258-11062b-4b7c9a8e48334d5aad2fd274fddba3bc-mv2.jpg',
                'posts' => [
                    [
                        'author' => 'Santana Prime',
                        'date' => 'May 10',
                        'read_time' => '2 min read',
                        'title' => 'Cleaning Services in Torrevieja: What You Need to Know',
                        'excerpt' => 'Keeping your holiday home in Torrevieja spotless is essential to getting the most out of it. Cleaning not only improves the atmosphere, but also protects the...',
                        'image' => '/assets/wix-assets/7365b9f7f8289696-c50f24-edc8d45c0fe040bf92882e427f1fbcf2-mv2.png',
                        'avatar' => '/assets/wix-assets/a88ffdf9c7987751-11062b-ba29744d482846cfb08835f7419128a1-mv2.jpg',
                        'views' => '0 views',
                        'comments' => '0 comments',
                    ],
                    [
                        'author' => 'Santana Prime',
                        'date' => 'Aug 2, 2025',
                        'read_time' => '1 min read',
                        'title' => 'More bookings, at better prices and happier guest.',
                        'excerpt' => 'We love your property and we would like to manage it for you. Santana - Torrevieja: Your partner for short-Term property management. At...',
                        'avatar' => '/assets/wix-assets/a88ffdf9c7987751-11062b-ba29744d482846cfb08835f7419128a1-mv2.jpg',
                        'views' => '5 views',
                        'comments' => '0 comments',
                    ],
                ],
            ],
            [
                'type' => 'contact',
                'heading' => ['en' => "Cleanliness isn't just what we do-it's who we are.\nPassion, precision, and professionalism in every detail.", 'es' => 'La limpieza no es solo lo que hacemos: es lo que somos.'],
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
            ],
        ];
    }

    private function homeBlocks(): array
    {
        return [
            [
                'type' => 'hero_image',
                'image' => '/assets/wix-assets/56ac427a7edc4ce6-modern-family-home.jpg',
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
                    '/assets/wix-assets/3ccb3288cfb8248a-modern-luxury-house.jpg',
                    '/assets/wix-assets/d236d292e96ff00c-building-inspection.jpeg',
                    '/assets/wix-assets/48865763e2e52f40-postbox.jpeg',
                    '/assets/wix-assets/2d499f7198bbd35a-man-inspecting-entrance.jpeg',
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
                    '/assets/wix-assets/cedd63ecb52f77f8-cleaning-team-portrait.jpg',
                    '/assets/wix-assets/0508cb0453d86f4e-cleaning-the-windows.jpeg',
                    '/assets/wix-assets/da5482a7b76c1410-bathroom-cleaner.jpg',
                    '/assets/wix-assets/4e1bcf870e1bf665-hotel-laundry-room.jpg',
                    '/assets/wix-assets/e5a80bb660436f51-handing-over-keys.jpg',
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
                'image' => '/assets/wix-assets/397361307f8c6938-office-clean.png',
                'actions' => [
                    ['label' => '*Check our offer', 'url' => '/en/category/tourist-rental-service'],
                    ['label' => 'Contact us', 'url' => 'https://api.whatsapp.com/send?phone=34624229511'],
                ],
            ],
            [
                'type' => 'gallery',
                'images' => [
                    '/assets/wix-assets/904277086ea40e37-putzfrauen.jpg',
                    '/assets/wix-assets/ab6e895963dc140b-cleaning-with-a-mop.jpg',
                    '/assets/wix-assets/3007cfbf3956d095-basket-of-linens.jpeg',
                    '/assets/wix-assets/dd0c995a3567fae4-ein-bett-machen.jpg',
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
                'left_image' => '/assets/wix-assets/e0224388eb048fa9-a-vertical-image-of-home-cleaning-service.jpg',
                'right_image' => '/assets/wix-assets/a899f9840b79fa84-a-vertical-image-of-a-home-cleaning-service.jpg',
            ],
        ];
    }
}
