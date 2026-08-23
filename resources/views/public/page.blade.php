<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->localized('title', $locale) }} - Hola Santana</title>
    <meta name="description" content="{{ $page->localized('meta_description', $locale) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $isPrimeTemplate = in_array($page->template, ['home', 'prime'], true);
    $languageFlags = ['en' => '🇬🇧', 'es' => '🇪🇸', 'de' => '🇩🇪', 'sv' => '🇸🇪', 'fi' => '🇫🇮'];
    $languageNames = ['en' => 'English', 'es' => 'Spanish', 'de' => 'German', 'sv' => 'Swedish', 'fi' => 'Finnish'];
    $languageLabels = ['en' => 'English (US)', 'es' => 'Spanish', 'de' => 'German', 'sv' => 'Swedish', 'fi' => 'Finnish'];
    $submitQueryLabels = [
        'en' => 'Submit query',
        'es' => 'Enviar consulta',
        'de' => 'Anfrage senden',
        'sv' => 'Skicka fraga',
        'fi' => 'Laheta kysely',
    ];
    $queryFormLabels = [
        'en' => [
            'heading' => 'Submit your order / query',
            'intro' => 'Share the service details, preferred time, and best contact method. Santana Prime will follow up directly.',
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'telephone' => 'Telephone number',
            'email' => 'Email',
            'address' => 'Property address',
            'order_date' => 'Ordering date',
            'service_area' => 'Service area',
            'service_date' => 'Service date',
            'service_time' => 'Approximate service time',
            'contact_through' => 'Prefers to contact through',
            'message' => 'Your Message',
            'submit' => 'Submit query',
            'close' => 'Close form',
        ],
        'es' => [
            'heading' => 'Envia tu pedido / consulta',
            'intro' => 'Comparte los detalles del servicio, hora preferida y mejor metodo de contacto. Santana Prime respondera directamente.',
            'first_name' => 'Nombre',
            'last_name' => 'Apellido',
            'telephone' => 'Numero de telefono',
            'email' => 'Correo electronico',
            'address' => 'Direccion de la propiedad',
            'order_date' => 'Fecha del pedido',
            'service_area' => 'Area de servicio',
            'service_date' => 'Fecha del servicio',
            'service_time' => 'Hora aproximada del servicio',
            'contact_through' => 'Prefiere contactar a traves de',
            'message' => 'Su mensaje',
            'submit' => 'Enviar consulta',
            'close' => 'Cerrar formulario',
        ],
    ];
    $queryLabels = $queryFormLabels[$locale] ?? $queryFormLabels['en'];
    $queryServices = [
        'Holiday rental cleaning',
        'Private home cleaning',
        'Key holding',
        'Laundry service',
        'Property inspection',
        'Airport transfer',
        'Other',
    ];
    $queryContactMethods = ['Email', 'WhatsApp', 'Telephone'];
@endphp
<body class="{{ $isPrimeTemplate ? 'prime-site' : 'site' }} page-{{ str_replace(['/', '_'], '-', $page->slug) }}">
    @if ($isPrimeTemplate)
        <header class="prime-header">
            <div class="prime-tools">
                <a class="prime-mini-brand" href="{{ route('pages.show', ['locale' => $locale, 'slug' => 'home']) }}">Santana Prime</a>
                <a href="{{ route('owner.login') }}">Clients Login</a>
            </div>
            <div class="prime-brand-row">
                <img src="https://static.wixstatic.com/media/c50f24_80b75f48949d41deaf57c5edaedaae72~mv2.png/v1/fill/w_112,h_108,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Prime_logo_pdf%20(1)_pdf%20(1).png" alt="Santana Prime logo" decoding="async">
                <strong>Santana Prime</strong>
                <button class="mobile-menu-toggle" type="button" aria-expanded="false" aria-controls="prime-navigation" data-mobile-menu-toggle>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span class="sr-only">Menu</span>
                </button>
                <nav class="prime-nav" id="prime-navigation" data-mobile-menu>
                    @foreach ($menuItems as $item)
                        @if ($item->page?->slug === 'about-3')
                            <button class="prime-nav-query" type="button" data-submit-query-open>{{ $submitQueryLabels[$locale] ?? $submitQueryLabels['en'] }}</button>
                        @endif
                        <a @class(['active' => $item->page?->slug === $page->slug]) href="{{ $item->href($locale) }}" target="{{ $item->target }}">{{ $item->localizedLabel($locale) }}</a>
                    @endforeach
                </nav>
                <div class="prime-actions">
                    <div class="language-switcher" data-language-switcher>
                        <button class="language-current" type="button" aria-expanded="false" data-language-toggle>
                            <span class="language-flag" aria-hidden="true">{{ $languageFlags[$locale] ?? strtoupper($locale) }}</span>
                            <span class="language-name">{{ $languageLabels[$locale] ?? strtoupper($locale) }}</span>
                            <span class="language-chevron" aria-hidden="true"></span>
                        </button>
                        <div class="language-menu">
                        @foreach (($availableLocales ?? ['en', 'es']) as $availableLocale)
                            <a @class(['active' => $locale === $availableLocale]) href="{{ route('pages.show', ['locale' => $availableLocale, 'slug' => $page->slug]) }}" aria-label="{{ $languageNames[$availableLocale] ?? strtoupper($availableLocale) }}">
                                <span class="language-menu-code">{{ strtoupper($availableLocale) }}</span>
                                <span class="language-flag" aria-hidden="true">{{ $languageFlags[$availableLocale] ?? strtoupper($availableLocale) }}</span>
                                <span class="language-name">{{ $languageLabels[$availableLocale] ?? ($languageNames[$availableLocale] ?? strtoupper($availableLocale)) }}</span>
                            </a>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @if (session('service_enquiry_status'))
            <div class="service-enquiry-toast" role="status">{{ session('service_enquiry_status') }}</div>
        @endif
        @if (session('submit_query_status'))
            <div class="service-enquiry-toast" role="status">{{ session('submit_query_status') }}</div>
        @endif
        @if ($errors->serviceEnquiry->any())
            <div class="service-enquiry-toast is-error" role="alert">Please check the order enquiry form and try again.</div>
        @endif
        @if ($errors->submitQuery->any())
            <div class="service-enquiry-toast is-error" role="alert">Please check the submit query form and try again.</div>
        @endif
        <main class="prime-main">
            @php
                $holidayHomeListRendered = false;
                $holidayHomeDetails = collect($page->content_blocks ?? [])
                    ->filter(fn ($block) => data_get($block, 'type') === 'rental_unit')
                    ->mapWithKeys(function ($block) {
                        $name = data_get($block, 'heading.en', data_get($block, 'heading', ''));

                        return [strtolower(trim((string) $name)) => $block];
                    });
            @endphp
            @foreach (($page->content_blocks ?? []) as $block)
                @php
                    $type = data_get($block, 'type', 'panel');
                @endphp
                @php
                    $blockItems = data_get($block, "items.$locale", data_get($block, 'items.en', data_get($block, 'items', [])));
                    $blockItems = is_array($blockItems)
                        ? $blockItems
                        : array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $blockItems))));
                @endphp

                @if ($type === 'hero_image')
                    <section class="prime-hero" style="--hero-image: url('{{ data_get($block, 'image') }}')">
                        <h1>{{ $page->localized('hero_title', $locale) }}</h1>
                        <p>{{ $page->localized('hero_subtitle', $locale) }}</p>
                    </section>
                @elseif ($type === 'hero_panel')
                    <section class="prime-panel prime-page-hero">
                        <h1>{{ $page->localized('hero_title', $locale) }}</h1>
                        <p>{{ $page->localized('hero_subtitle', $locale) }}</p>
                    </section>
                @elseif ($type === 'wide_image')
                    <section class="prime-wide-image">
                        <img src="{{ data_get($block, 'image') }}" alt="" loading="lazy" decoding="async">
                    </section>
                @elseif ($type === 'gallery')
                    <section class="prime-gallery is-count-{{ count(data_get($block, 'images', [])) }}">
                        @foreach (data_get($block, 'images', []) as $image)
                            <img src="{{ $image }}" alt="" loading="lazy" decoding="async">
                        @endforeach
                    </section>
                @elseif ($type === 'category_products')
                    <section class="prime-category">
                        <h1>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h1>
                        <div class="prime-products">
                            @foreach (data_get($block, 'products', []) as $product)
                                <article class="prime-product-card">
                                    <div class="prime-product-image">
                                        <img src="{{ data_get($product, 'image') }}" alt="{{ data_get($product, 'name') }}" loading="lazy" decoding="async">
                                    </div>
                                    <h2>{{ data_get($product, 'name') }}</h2>
                                    @if (filled(data_get($product, 'price')) || filled(data_get($product, 'sale_price')))
                                        <p class="prime-product-price">
                                            @if (filled(data_get($product, 'sale_price')) && filled(data_get($product, 'price')))
                                                <span class="prime-price-old">{{ data_get($product, 'price') }}</span>
                                            @endif
                                            <span>{{ data_get($product, 'sale_price') ?: data_get($product, 'price') }}</span>
                                        </p>
                                    @endif
                                    <button type="button" data-order-service="{{ data_get($product, 'name') }}">Order It</button>
                                </article>
                            @endforeach
                        </div>
                        @if (filled(data_get($block, "more_label.$locale", data_get($block, 'more_label.en'))))
                            <a class="prime-more-link" href="#">{{ data_get($block, "more_label.$locale", data_get($block, 'more_label.en')) }}</a>
                        @endif
                    </section>
                @elseif ($type === 'text_section')
                    <section class="prime-open-section prime-text-section {{ data_get($block, 'class') }}">
                        @if (filled(data_get($block, "heading.$locale", data_get($block, 'heading.en'))))
                            <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        @endif
                        @if (filled(data_get($block, "body.$locale", data_get($block, 'body.en'))))
                            <div class="prime-open-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        @endif
                        @if ($blockItems)
                            <ul class="prime-checks">
                                @foreach ($blockItems as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if (filled(data_get($block, "footer.$locale", data_get($block, 'footer.en'))))
                            <div class="prime-open-footer">{!! nl2br(e(data_get($block, "footer.$locale", data_get($block, 'footer.en')))) !!}</div>
                        @endif
                        @include('public.partials.prime-actions', ['actions' => data_get($block, 'actions', [])])
                    </section>
                @elseif ($type === 'open_intro')
                    <section class="prime-open-section prime-intro-section">
                        <h1>{{ $page->localized('hero_title', $locale) }}</h1>
                        <h2>{{ $page->localized('hero_subtitle', $locale) }}</h2>
                        <div class="prime-open-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        @if (filled(data_get($block, "footer.$locale", data_get($block, 'footer.en'))))
                            <div class="prime-open-footer">{!! nl2br(e(data_get($block, "footer.$locale", data_get($block, 'footer.en')))) !!}</div>
                        @endif
                    </section>
                @elseif ($type === 'service_section')
                    <section class="prime-open-section prime-service-section">
                        <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        @if (data_get($block, 'images'))
                            <div class="prime-service-images is-count-{{ count(data_get($block, 'images', [])) }}">
                                @foreach (data_get($block, 'images', []) as $image)
                                    <img src="{{ $image }}" alt="" loading="lazy" decoding="async">
                                @endforeach
                            </div>
                        @endif
                        <div class="prime-open-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                    </section>
                @elseif ($type === 'rental_unit' && $page->slug === 'home-rental')
                    @unless ($holidayHomeListRendered)
                        @include('public.partials.holiday-home-list', ['holidayHomes' => $holidayHomes, 'locale' => $locale, 'holidayHomeDetails' => $holidayHomeDetails])
                        @php
                            $holidayHomeListRendered = true;
                        @endphp
                    @endunless
                @elseif ($type === 'holiday_home_listing')
                    @include('public.partials.holiday-home-list', ['holidayHomes' => $holidayHomes, 'locale' => $locale, 'holidayHomeDetails' => $holidayHomeDetails])
                    @php
                        $holidayHomeListRendered = true;
                    @endphp
                @elseif ($type === 'rental_unit')
                    <section class="prime-rental-unit">
                        <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        @if (data_get($block, 'images'))
                            <div class="prime-rental-gallery is-count-{{ count(data_get($block, 'images', [])) }}">
                                @foreach (data_get($block, 'images', []) as $image)
                                    <img src="{{ $image }}" alt="" loading="lazy" decoding="async">
                                @endforeach
                            </div>
                        @endif
                        <div class="prime-open-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        @include('public.partials.prime-actions', ['actions' => data_get($block, 'actions', [])])
                    </section>
                @elseif ($type === 'sample_section')
                    <section class="prime-open-section prime-sample-section">
                        <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        @if (data_get($block, 'videos'))
                            <div class="prime-service-images is-count-{{ count(data_get($block, 'videos', [])) }}">
                                @foreach (data_get($block, 'videos', []) as $video)
                                    @if (is_array($video) && filled(data_get($video, 'src')))
                                        <video class="prime-video-player" src="{{ data_get($video, 'src') }}" poster="{{ data_get($video, 'poster') }}" controls preload="metadata"></video>
                                    @else
                                        <span class="prime-video-thumb">
                                            <img src="{{ $video }}" alt="" loading="lazy" decoding="async">
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <div class="prime-open-footer">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        @if (data_get($block, 'image'))
                            <img class="prime-sample-wide" src="{{ data_get($block, 'image') }}" alt="" loading="lazy" decoding="async">
                        @endif
                    </section>
                @elseif ($type === 'slider')
                    @php
                        $slides = data_get($block, 'slides', []);
                    @endphp
                    @if ($slides)
                        <section class="prime-slider" data-slider>
                            <div class="prime-slider-track">
                                @foreach ($slides as $index => $slide)
                                    <figure class="prime-slide {{ $index === 0 ? 'is-active' : '' }}" data-slide>
                                        <img src="{{ data_get($slide, 'image') }}" alt="{{ data_get($slide, 'title', '') }}" loading="lazy" decoding="async">
                                        <figcaption>
                                            <span>{{ data_get($slide, 'title') }}</span>
                                            <span>{{ $index + 1 }}/{{ count($slides) }}</span>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                            <button class="prime-slider-arrow is-prev" type="button" data-slider-prev aria-label="Previous slide">‹</button>
                            <button class="prime-slider-arrow is-next" type="button" data-slider-next aria-label="Next slide">›</button>
                        </section>
                    @endif
                @elseif ($type === 'split')
                    <section class="prime-panel prime-split">
                        <div>
                            <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                            <div class="prime-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                            @if ($blockItems)
                                <ul class="prime-checks">
                                    @foreach ($blockItems as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            @include('public.partials.prime-actions', ['actions' => data_get($block, 'actions', [])])
                        </div>
                        <img src="{{ data_get($block, 'image') }}" alt="" loading="lazy" decoding="async">
                    </section>
                @elseif ($type === 'media_text')
                    <section class="prime-panel prime-media-text {{ data_get($block, 'reverse') ? 'is-reverse' : '' }}">
                        <img src="{{ data_get($block, 'image') }}" alt="" loading="lazy" decoding="async">
                        <div>
                            <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                            <div class="prime-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                            @if ($blockItems)
                                <ul class="prime-checks">
                                    @foreach ($blockItems as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </section>
                @elseif ($type === 'about_intro')
                    <section class="about-intro">
                        <h1>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h1>
                        <div>{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                    </section>
                @elseif ($type === 'about_feature')
                    <section class="about-feature {{ data_get($block, 'reverse') ? 'is-reverse' : '' }}">
                        <img src="{{ data_get($block, 'image') }}" alt="" loading="lazy" decoding="async">
                        <div>
                            <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                            <div>{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        </div>
                    </section>
                @elseif ($type === 'blog_listing')
                    <section class="blog-shell" style="--blog-side-image: url('{{ data_get($block, 'side_image') }}')">
                        <div class="blog-content">
                            <a class="blog-filter" href="#">{{ data_get($block, "filter_label.$locale", data_get($block, 'filter_label.en')) }}</a>
                            <h1>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h1>
                            <div class="blog-posts">
                                @foreach (data_get($block, 'posts', []) as $post)
                                    <article class="blog-card {{ filled(data_get($post, 'image')) ? 'has-image' : 'is-text-only' }}">
                                        @if (filled(data_get($post, 'image')))
                                            <img class="blog-card-image" src="{{ data_get($post, 'image') }}" alt="" loading="lazy" decoding="async">
                                        @endif
                                        <div class="blog-card-body">
                                            <div class="blog-meta">
                                                @if (filled(data_get($post, 'avatar')))
                                                    <img src="{{ data_get($post, 'avatar') }}" alt="" loading="lazy" decoding="async">
                                                @endif
                                                <div>
                                                    <strong>{{ data_get($post, 'author', 'Santana Prime') }}</strong>
                                                    <span>{{ data_get($post, 'date') }} &middot; {{ data_get($post, 'read_time') }}</span>
                                                </div>
                                                <button type="button" aria-label="Post actions">&#8942;</button>
                                            </div>
                                            <h2>{{ data_get($post, 'title') }}</h2>
                                            <p>{{ data_get($post, 'excerpt') }}</p>
                                            <footer>
                                                <span>{{ data_get($post, 'views', '0 views') }}</span>
                                                <span>{{ data_get($post, 'comments', '0 comments') }}</span>
                                                <span class="blog-heart">&#9825;</span>
                                            </footer>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @elseif ($type === 'contact_page')
                    <section class="contact-page">
                        <section class="contact-hero-copy">
                            <h1>{{ $page->localized('hero_title', $locale) }}</h1>
                            <p>{{ $page->localized('hero_subtitle', $locale) }}</p>
                        </section>
                        <section class="contact-live-card">
                            <div class="contact-poster-column">
                                <img class="contact-poster" src="{{ data_get($block, 'poster') }}" alt="" loading="lazy" decoding="async">
                                <h2>{{ data_get($block, "location_heading.$locale", data_get($block, 'location_heading.en')) }}</h2>
                                <div class="contact-office-photos">
                                    @foreach (data_get($block, 'office_images', []) as $image)
                                        <img src="{{ $image }}" alt="" loading="lazy" decoding="async">
                                    @endforeach
                                </div>
                                <p>{{ data_get($block, "location_body.$locale", data_get($block, 'location_body.en')) }}</p>
                                <address>
                                    @foreach (data_get($block, 'address', []) as $line)
                                        {{ $line }}<br>
                                    @endforeach
                                </address>
                            </div>
                            <form class="contact-live-form" action="mailto:spm3182@gmail.com" method="post" enctype="text/plain">
                                <h2>{{ data_get($block, "form_heading.$locale", data_get($block, 'form_heading.en')) }}</h2>
                                <p>{{ data_get($block, "form_intro.$locale", data_get($block, 'form_intro.en')) }}</p>
                                <input type="text" name="name" placeholder="Name">
                                <input type="email" name="email" placeholder="Email">
                                <textarea name="message" rows="8" placeholder="Type your message here..."></textarea>
                                <button type="submit">Send message</button>
                            </form>
                            <iframe
                                class="contact-map"
                                src="https://maps.google.com/maps?q=Calle%20Ulpiano%2071%20Torrevieja%20Spain&t=&z=14&ie=UTF8&iwloc=&output=embed"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Office Torrevieja map"></iframe>
                        </section>
                    </section>
                @elseif ($type === 'faq_section')
                    <section class="prime-faq-section">
                        <div class="prime-faq-intro">
                            <h1>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en', 'Frequently asked questions')) }}</h1>
                            @if (filled(data_get($block, "body.$locale", data_get($block, 'body.en'))))
                                <p>{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</p>
                            @endif
                        </div>
                        <div class="prime-faq-list">
                            @foreach (data_get($block, 'faqs', []) as $index => $faq)
                                @php
                                    $question = data_get($faq, "question.$locale", data_get($faq, 'question.en', data_get($faq, 'question')));
                                    $answer = data_get($faq, "answer.$locale", data_get($faq, 'answer.en', data_get($faq, 'answer')));
                                @endphp
                                @if (filled($question) || filled($answer))
                                    <details class="prime-faq-item" {{ $index === 0 ? 'open' : '' }}>
                                        <summary>{{ $question ?: 'Question' }}</summary>
                                        <div class="prime-faq-answer">{!! nl2br(e($answer)) !!}</div>
                                    </details>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @elseif ($type === 'faq_order_form')
                    @php
                        $formLabels = [
                            'en' => [
                                'first_name' => 'First name',
                                'last_name' => 'Last name',
                                'telephone' => 'Telephone number',
                                'email' => 'Email',
                                'address' => 'Property address',
                                'order_date' => 'Ordering date',
                                'service_area' => 'Service area',
                                'service_date' => 'Service date',
                                'service_time' => 'Approximate service time',
                                'contact_through' => 'Prefers to contact through',
                                'message' => 'Your Message',
                                'submit' => 'Submit query',
                            ],
                            'es' => [
                                'first_name' => 'Nombre',
                                'last_name' => 'Apellido',
                                'telephone' => 'Numero de telefono',
                                'email' => 'Correo electronico',
                                'address' => 'Direccion de la propiedad',
                                'order_date' => 'Fecha del pedido',
                                'service_area' => 'Area de servicio',
                                'service_date' => 'Fecha del servicio',
                                'service_time' => 'Hora aproximada del servicio',
                                'contact_through' => 'Prefiere contactar a traves de',
                                'message' => 'Su mensaje',
                                'submit' => 'Enviar consulta',
                            ],
                        ];
                        $labels = $formLabels[$locale] ?? $formLabels['en'];
                    @endphp
                    <section class="faq-order-section" id="submit-query">
                        <h1>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h1>
                        <form class="faq-order-form" action="{{ route('submit-query.store') }}" method="post">
                            @csrf
                            <label>{{ $labels['first_name'] }} *<input type="text" name="first_name" required></label>
                            <label>{{ $labels['last_name'] }} *<input type="text" name="last_name" required></label>
                            <label>{{ $labels['telephone'] }} *<input type="tel" name="telephone" required></label>
                            <label>{{ $labels['email'] }}<input type="email" name="email"></label>
                            <label>{{ $labels['address'] }}<input type="text" name="property_address"></label>
                            <label>{{ $labels['order_date'] }}<input type="date" name="ordering_date"></label>
                            <label>
                                {{ $labels['service_area'] }}
                                <select name="service_area">
                                    <option value=""></option>
                                    @foreach (data_get($block, 'services', []) as $service)
                                        <option>{{ $service }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>{{ $labels['service_date'] }}<input type="date" name="service_date"></label>
                            <label>{{ $labels['service_time'] }}<input type="time" name="service_time"></label>
                            <fieldset>
                                <legend>{{ $labels['contact_through'] }}</legend>
                                @foreach (data_get($block, 'contact_methods', []) as $method)
                                    <label><input type="radio" name="contact_method" value="{{ $method }}"> {{ $method }}</label>
                                @endforeach
                            </fieldset>
                            <label>{{ $labels['message'] }} *<textarea name="message" rows="5" required></textarea></label>
                            <button type="submit">{{ $labels['submit'] }}</button>
                        </form>
                    </section>
                @elseif ($type === 'contact')
                    @php
                        $contactItems = data_get($block, 'contact_items', [
                            ['label' => ['en' => 'Get in touch with us'], 'text' => ['en' => 'Envíenos un correo electrónico'], 'url' => 'mailto:info@holasantana.com'],
                            ['label' => [], 'text' => ['en' => 'Llámenos'], 'url' => 'tel:+34624229511'],
                            ['label' => ['en' => 'Whatsapp'], 'text' => ['en' => 'Contact us'], 'url' => 'https://api.whatsapp.com/send?phone=34624229511'],
                        ]);
                        $socialHeading = data_get($block, "social_heading.$locale", data_get($block, 'social_heading.en', 'Follow us at'));
                        $socialLinks = data_get($block, 'social_links', [
                            ['icon' => 'f', 'label' => 'Facebook', 'url' => '#'],
                            ['icon' => '◎', 'label' => 'Instagram', 'url' => '#'],
                            ['icon' => '▶', 'label' => 'YouTube', 'url' => '#'],
                        ]);
                    @endphp
                    <section class="prime-contact">
                        <img src="{{ data_get($block, 'left_image') }}" alt="" loading="lazy" decoding="async">
                        <div class="prime-panel">
                            <h2>{!! nl2br(e(data_get($block, "heading.$locale", data_get($block, 'heading.en')))) !!}</h2>
                            <div class="contact-grid">
                                @foreach ($contactItems as $item)
                                    @php
                                        $itemLabel = data_get($item, "label.$locale", data_get($item, 'label.en', data_get($item, 'label')));
                                        $itemText = data_get($item, "text.$locale", data_get($item, 'text.en', data_get($item, 'text')));
                                        $itemUrl = data_get($item, 'url', '#');
                                    @endphp
                                    @if (filled($itemLabel))
                                        <strong>{{ $itemLabel }}</strong>
                                    @endif
                                    @if (filled($itemText))
                                        <a href="{{ $itemUrl }}">{{ $itemText }}</a>
                                    @endif
                                @endforeach
                                @if (filled($socialHeading))
                                    <strong>{{ $socialHeading }}</strong>
                                @endif
                                <div class="social-row">
                                    @foreach ($socialLinks as $social)
                                        @if (filled(data_get($social, 'url')))
                                            <a href="{{ data_get($social, 'url') }}" aria-label="{{ data_get($social, 'label', data_get($social, 'icon')) }}">{{ data_get($social, 'icon') }}</a>
                                        @else
                                            <span>{{ data_get($social, 'icon') }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <img src="{{ data_get($block, 'right_image') }}" alt="" loading="lazy" decoding="async">
                    </section>
                @else
                    <section class="prime-panel">
                        <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        <div class="prime-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        @if ($blockItems)
                            <ul class="prime-checks">
                                @foreach ($blockItems as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if (filled(data_get($block, "footer.$locale", data_get($block, 'footer.en'))))
                            <div class="prime-footer-copy">{!! nl2br(e(data_get($block, "footer.$locale", data_get($block, 'footer.en')))) !!}</div>
                        @endif
                        @include('public.partials.prime-actions', ['actions' => data_get($block, 'actions', [])])
                    </section>
                @endif
            @endforeach
            @if ($page->slug === 'home-rental' && ! $holidayHomeListRendered)
                @include('public.partials.holiday-home-list', ['holidayHomes' => $holidayHomes, 'locale' => $locale, 'holidayHomeDetails' => $holidayHomeDetails])
            @endif
        </main>
        <footer class="prime-footer">
            <div>
                <strong>Santana Prime</strong>
                <span>{{ $footerSettings['description'] }}</span>
            </div>
            <div>
                <a href="mailto:{{ $footerSettings['email'] }}">{{ $footerSettings['email'] }}</a>
                <a href="tel:{{ $footerSettings['phone_href'] }}">{{ $footerSettings['phone'] }}</a>
            </div>
        </footer>
        <div class="submit-query-modal @if ($errors->submitQuery->any()) is-open @endif" data-submit-query-modal aria-hidden="{{ $errors->submitQuery->any() ? 'false' : 'true' }}">
            <div class="submit-query-backdrop" data-submit-query-close></div>
            <form class="submit-query-dialog" action="{{ route('submit-query.store') }}" method="post">
                @csrf
                <div class="submit-query-head">
                    <div>
                        <span>Service request</span>
                        <h2>{{ $queryLabels['heading'] }}</h2>
                        <p>{{ $queryLabels['intro'] }}</p>
                    </div>
                    <button type="button" aria-label="{{ $queryLabels['close'] }}" data-submit-query-close>×</button>
                </div>
                <div class="submit-query-grid">
                    <label>{{ $queryLabels['first_name'] }} *<input type="text" name="first_name" required></label>
                    <label>{{ $queryLabels['last_name'] }} *<input type="text" name="last_name" required></label>
                    <label>{{ $queryLabels['telephone'] }} *<input type="tel" name="telephone" required></label>
                    <label>{{ $queryLabels['email'] }}<input type="email" name="email"></label>
                    <label class="wide">{{ $queryLabels['address'] }}<input type="text" name="property_address"></label>
                    <label>{{ $queryLabels['order_date'] }}<input type="date" name="ordering_date"></label>
                    <label>
                        {{ $queryLabels['service_area'] }}
                        <select name="service_area">
                            <option value=""></option>
                            @foreach ($queryServices as $service)
                                <option>{{ $service }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>{{ $queryLabels['service_date'] }}<input type="date" name="service_date"></label>
                    <label>{{ $queryLabels['service_time'] }}<input type="time" name="service_time"></label>
                    <fieldset class="wide">
                        <legend>{{ $queryLabels['contact_through'] }}</legend>
                        <div>
                            @foreach ($queryContactMethods as $method)
                                <label><input type="radio" name="contact_method" value="{{ $method }}"> {{ $method }}</label>
                            @endforeach
                        </div>
                    </fieldset>
                    <label class="wide">{{ $queryLabels['message'] }} *<textarea name="message" rows="5" required></textarea></label>
                </div>
                <div class="submit-query-actions">
                    <button type="submit">{{ $queryLabels['submit'] }}</button>
                    <button type="button" data-submit-query-close>{{ $queryLabels['close'] }}</button>
                </div>
            </form>
        </div>
        <div class="service-order-modal @if ($errors->serviceEnquiry->any()) is-open @endif" data-service-order-modal aria-hidden="{{ $errors->serviceEnquiry->any() ? 'false' : 'true' }}">
            <div class="service-order-backdrop" data-service-order-close></div>
            <form class="service-order-dialog" method="post" action="{{ route('service-enquiries.store') }}">
                @csrf
                <div class="service-order-head">
                    <div>
                        <span>Service enquiry</span>
                        <h2>Order It</h2>
                    </div>
                    <button type="button" aria-label="Close form" data-service-order-close>×</button>
                </div>
                <label>
                    Service Name
                    <input type="text" name="service_name" value="{{ old('service_name') }}" readonly data-service-order-name>
                    @error('service_name', 'serviceEnquiry')<small>{{ $message }}</small>@enderror
                </label>
                <label>
                    Enquiry Date
                    <input type="date" name="enquiry_date" value="{{ old('enquiry_date', now()->toDateString()) }}">
                    @error('enquiry_date', 'serviceEnquiry')<small>{{ $message }}</small>@enderror
                </label>
                <label>
                    Name *
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name', 'serviceEnquiry')<small>{{ $message }}</small>@enderror
                </label>
                <label>
                    Email Address *
                    <input type="email" name="email" value="{{ old('email') }}" required>
                    @error('email', 'serviceEnquiry')<small>{{ $message }}</small>@enderror
                </label>
                <label>
                    Telephone Number *
                    <input type="tel" name="telephone" value="{{ old('telephone') }}" required>
                    @error('telephone', 'serviceEnquiry')<small>{{ $message }}</small>@enderror
                </label>
                <div class="service-order-actions">
                    <button type="submit">Send</button>
                    <button type="button" data-service-order-close>Exit</button>
                </div>
            </form>
        </div>
    @else
        <header class="site-header">
            <a class="site-brand" href="{{ route('pages.show', ['locale' => $locale, 'slug' => 'home']) }}">Hola Santana</a>
            <nav>
                @foreach ($menuItems as $item)
                    <a @class(['active' => $item->page?->slug === $page->slug]) href="{{ $item->href($locale) }}" target="{{ $item->target }}">{{ $item->localizedLabel($locale) }}</a>
                @endforeach
            </nav>
            <div class="language-switcher" data-language-switcher>
                <button class="language-current" type="button" aria-expanded="false" data-language-toggle>
                    <span class="language-flag" aria-hidden="true">{{ $languageFlags[$locale] ?? strtoupper($locale) }}</span>
                    <span class="language-name">{{ $languageLabels[$locale] ?? strtoupper($locale) }}</span>
                    <span class="language-chevron" aria-hidden="true"></span>
                </button>
                <div class="language-menu">
                @foreach (($availableLocales ?? ['en', 'es']) as $availableLocale)
                    <a @class(['active' => $locale === $availableLocale]) href="{{ route('pages.show', ['locale' => $availableLocale, 'slug' => $page->slug]) }}" aria-label="{{ $languageNames[$availableLocale] ?? strtoupper($availableLocale) }}">
                        <span class="language-menu-code">{{ strtoupper($availableLocale) }}</span>
                        <span class="language-flag" aria-hidden="true">{{ $languageFlags[$availableLocale] ?? strtoupper($availableLocale) }}</span>
                        <span class="language-name">{{ $languageLabels[$availableLocale] ?? ($languageNames[$availableLocale] ?? strtoupper($availableLocale)) }}</span>
                    </a>
                @endforeach
                </div>
            </div>
        </header>
        <main>
            <section class="hero">
                <p>{{ $page->localized('hero_eyebrow', $locale) }}</p>
                <h1>{{ $page->localized('hero_title', $locale) }}</h1>
                <div>{{ $page->localized('hero_subtitle', $locale) }}</div>
            </section>
            <section class="content-sections">
                @foreach (($page->content_blocks ?? []) as $block)
                    <article class="content-section">
                        <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        <p>{{ data_get($block, "body.$locale", data_get($block, 'body.en')) }}</p>
                        @if (filled(data_get($block, 'button_url')))
                            <a class="site-button" href="{{ data_get($block, 'button_url') }}">{{ data_get($block, "button_text.$locale", data_get($block, 'button_text.en', 'Learn more')) }}</a>
                        @endif
                    </article>
                @endforeach
            </section>
        </main>
        <footer class="site-footer">
            <span>Hola Santana</span>
            <span>{{ $footerSettings['description'] }}</span>
        </footer>
    @endif
</body>
</html>
