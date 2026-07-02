<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->localized('title', $locale) }} - Hola Santana</title>
    <meta name="description" content="{{ $page->localized('meta_description', $locale) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php($isPrimeTemplate = in_array($page->template, ['home', 'prime'], true))
@php($languageFlags = ['en' => '🇬🇧', 'es' => '🇪🇸', 'de' => '🇩🇪', 'sv' => '🇸🇪', 'fi' => '🇫🇮'])
@php($languageNames = ['en' => 'English', 'es' => 'Spanish', 'de' => 'German', 'sv' => 'Swedish', 'fi' => 'Finnish'])
@php($languageLabels = ['en' => 'English (US)', 'es' => 'Spanish', 'de' => 'German', 'sv' => 'Swedish', 'fi' => 'Finnish'])
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
        @if ($errors->serviceEnquiry->any())
            <div class="service-enquiry-toast is-error" role="alert">Please check the order enquiry form and try again.</div>
        @endif
        <main class="prime-main">
            @php($holidayHomeListRendered = false)
            @foreach (($page->content_blocks ?? []) as $block)
                @php($type = data_get($block, 'type', 'panel'))

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
                    <section class="prime-open-section">
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
                        @include('public.partials.holiday-home-list', ['holidayHomes' => $holidayHomes, 'locale' => $locale])
                        @php($holidayHomeListRendered = true)
                    @endunless
                @elseif ($type === 'holiday_home_listing')
                    @include('public.partials.holiday-home-list', ['holidayHomes' => $holidayHomes, 'locale' => $locale])
                    @php($holidayHomeListRendered = true)
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
                    @php($slides = data_get($block, 'slides', []))
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
                            @if (data_get($block, 'items'))
                                <ul class="prime-checks">
                                    @foreach (data_get($block, 'items', []) as $item)
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
                            @if (data_get($block, 'items'))
                                <ul class="prime-checks">
                                    @foreach (data_get($block, 'items', []) as $item)
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
                @elseif ($type === 'faq_order_form')
                    <section class="faq-order-section">
                        <h1>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h1>
                        <form class="faq-order-form" action="mailto:spm3182@gmail.com" method="post" enctype="text/plain">
                            <label>Nombre *<input type="text" name="nombre" required></label>
                            <label>Apellido *<input type="text" name="apellido" required></label>
                            <label>Número de teléfono *<input type="tel" name="telefono" placeholder="🇪🇸" required></label>
                            <label>Correo electrónico<input type="email" name="email"></label>
                            <label>Dirección de la propiedad<input type="text" name="direccion"></label>
                            <label>Fecha del pedido<input type="date" name="fecha_pedido"></label>
                            <label>
                                Area de servicio
                                <select name="area_servicio">
                                    <option value=""></option>
                                    @foreach (data_get($block, 'services', []) as $service)
                                        <option>{{ $service }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label>Fecha del servicio<input type="date" name="fecha_servicio"></label>
                            <label>Hora de iniciar el servicio<input type="time" name="hora_servicio"></label>
                            <fieldset>
                                <legend>Prefiere contactar a través de</legend>
                                @foreach (data_get($block, 'contact_methods', []) as $method)
                                    <label><input type="radio" name="contacto" value="{{ $method }}"> {{ $method }}</label>
                                @endforeach
                            </fieldset>
                            <label>Su mensaje *<textarea name="mensaje" rows="5" required></textarea></label>
                            <button type="submit">Enviar</button>
                        </form>
                    </section>
                @elseif ($type === 'contact')
                    <section class="prime-contact">
                        <img src="{{ data_get($block, 'left_image') }}" alt="" loading="lazy" decoding="async">
                        <div class="prime-panel">
                            <h2>{!! nl2br(e(data_get($block, "heading.$locale", data_get($block, 'heading.en')))) !!}</h2>
                            <div class="contact-grid">
                                <strong>Get in touch with us</strong>
                                <a href="mailto:info@holasantana.com">Envíenos un correo electrónico</a>
                                <a href="tel:+34624229511">Llámenos</a>
                                <strong>Follow us at</strong>
                                <div class="social-row">
                                    <span>f</span><span>◎</span><span>▶</span>
                                </div>
                                <strong>Whatsapp</strong>
                                <a href="https://api.whatsapp.com/send?phone=34624229511">Contact us</a>
                            </div>
                        </div>
                        <img src="{{ data_get($block, 'right_image') }}" alt="" loading="lazy" decoding="async">
                    </section>
                @else
                    <section class="prime-panel">
                        <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        <div class="prime-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        @if (data_get($block, 'items'))
                            <ul class="prime-checks">
                                @foreach (data_get($block, 'items', []) as $item)
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
                @include('public.partials.holiday-home-list', ['holidayHomes' => $holidayHomes, 'locale' => $locale])
            @endif
        </main>
        <footer class="prime-footer">
            <div>
                <strong>Santana Prime</strong>
                <span>Home care and holiday rental management in Torrevieja.</span>
            </div>
            <div>
                <a href="mailto:info@holasantana.com">info@holasantana.com</a>
                <a href="tel:+34624229511">+34 624 229 511</a>
            </div>
        </footer>
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
            <span>Dynamic CMS foundation powered by Laravel</span>
        </footer>
    @endif
</body>
</html>
