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
<body class="{{ $isPrimeTemplate ? 'prime-site' : 'site' }} page-{{ str_replace(['/', '_'], '-', $page->slug) }}">
    @if ($isPrimeTemplate)
        <header class="prime-header">
            <div class="prime-tools">
                <span></span>
                <a href="{{ route('admin.login') }}">Clients Login</a>
            </div>
            <nav class="prime-nav">
                @foreach ($menuItems as $item)
                    <a href="{{ $item->href($locale) }}" target="{{ $item->target }}">{{ $item->localizedLabel($locale) }}</a>
                @endforeach
            </nav>
            <div class="prime-brand-row">
                <img src="https://static.wixstatic.com/media/c50f24_80b75f48949d41deaf57c5edaedaae72~mv2.png/v1/fill/w_112,h_108,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/Prime_logo_pdf%20(1)_pdf%20(1).png" alt="Santana Prime logo">
                <strong>Santana Prime</strong>
                <div class="prime-actions">
                    <div class="language-switcher">
                        <a @class(['active' => $locale === 'en']) href="{{ route('pages.show', ['locale' => 'en', 'slug' => $page->slug]) }}">EN</a>
                        <a @class(['active' => $locale === 'es']) href="{{ route('pages.show', ['locale' => 'es', 'slug' => $page->slug]) }}">ES</a>
                    </div>
                    <span class="cart-icon">0</span>
                </div>
            </div>
        </header>
        <main class="prime-main">
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
                        <img src="{{ data_get($block, 'image') }}" alt="">
                    </section>
                @elseif ($type === 'gallery')
                    <section class="prime-gallery is-count-{{ count(data_get($block, 'images', [])) }}">
                        @foreach (data_get($block, 'images', []) as $image)
                            <img src="{{ $image }}" alt="">
                        @endforeach
                    </section>
                @elseif ($type === 'category_products')
                    <section class="prime-category">
                        <h1>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h1>
                        <div class="prime-products">
                            @foreach (data_get($block, 'products', []) as $product)
                                <article class="prime-product-card">
                                    <a class="prime-product-image" href="#" aria-label="Vista rapida">
                                        <img src="{{ data_get($product, 'image') }}" alt="{{ data_get($product, 'name') }}">
                                        <span>Vista rapida</span>
                                    </a>
                                    <h2>{{ data_get($product, 'name') }}</h2>
                                    <div class="prime-product-price">
                                        @if (filled(data_get($product, 'sale_price')))
                                            <span class="prime-price-old">{{ data_get($product, 'price') }}</span>
                                            <span>{{ data_get($product, 'sale_price') }}</span>
                                        @else
                                            <span>{{ data_get($product, 'price') }}</span>
                                        @endif
                                    </div>
                                    <button type="button">Add to Cart</button>
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
                                    <img src="{{ $image }}" alt="">
                                @endforeach
                            </div>
                        @endif
                        <div class="prime-open-copy">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                    </section>
                @elseif ($type === 'rental_unit')
                    <section class="prime-rental-unit">
                        <h2>{{ data_get($block, "heading.$locale", data_get($block, 'heading.en')) }}</h2>
                        @if (data_get($block, 'images'))
                            <div class="prime-rental-gallery is-count-{{ count(data_get($block, 'images', [])) }}">
                                @foreach (data_get($block, 'images', []) as $image)
                                    <img src="{{ $image }}" alt="">
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
                                            <img src="{{ $video }}" alt="">
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        <div class="prime-open-footer">{!! nl2br(e(data_get($block, "body.$locale", data_get($block, 'body.en')))) !!}</div>
                        @if (data_get($block, 'image'))
                            <img class="prime-sample-wide" src="{{ data_get($block, 'image') }}" alt="">
                        @endif
                    </section>
                @elseif ($type === 'slider')
                    @php($slides = data_get($block, 'slides', []))
                    @if ($slides)
                        <section class="prime-slider" data-slider>
                            <div class="prime-slider-track">
                                @foreach ($slides as $index => $slide)
                                    <figure class="prime-slide {{ $index === 0 ? 'is-active' : '' }}" data-slide>
                                        <img src="{{ data_get($slide, 'image') }}" alt="{{ data_get($slide, 'title', '') }}">
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
                        <img src="{{ data_get($block, 'image') }}" alt="">
                    </section>
                @elseif ($type === 'media_text')
                    <section class="prime-panel prime-media-text {{ data_get($block, 'reverse') ? 'is-reverse' : '' }}">
                        <img src="{{ data_get($block, 'image') }}" alt="">
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
                        <img src="{{ data_get($block, 'image') }}" alt="">
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
                                            <img class="blog-card-image" src="{{ data_get($post, 'image') }}" alt="">
                                        @endif
                                        <div class="blog-card-body">
                                            <div class="blog-meta">
                                                @if (filled(data_get($post, 'avatar')))
                                                    <img src="{{ data_get($post, 'avatar') }}" alt="">
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
                                <img class="contact-poster" src="{{ data_get($block, 'poster') }}" alt="">
                                <h2>{{ data_get($block, "location_heading.$locale", data_get($block, 'location_heading.en')) }}</h2>
                                <div class="contact-office-photos">
                                    @foreach (data_get($block, 'office_images', []) as $image)
                                        <img src="{{ $image }}" alt="">
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
                @elseif ($type === 'contact')
                    <section class="prime-contact">
                        <img src="{{ data_get($block, 'left_image') }}" alt="">
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
                        <img src="{{ data_get($block, 'right_image') }}" alt="">
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
        </main>
    @else
        <header class="site-header">
            <a class="site-brand" href="{{ route('pages.show', ['locale' => $locale, 'slug' => 'home']) }}">Hola Santana</a>
            <nav>
                @foreach ($menuItems as $item)
                    <a href="{{ $item->href($locale) }}" target="{{ $item->target }}">{{ $item->localizedLabel($locale) }}</a>
                @endforeach
            </nav>
            <div class="language-switcher">
                <a @class(['active' => $locale === 'en']) href="{{ route('pages.show', ['locale' => 'en', 'slug' => $page->slug]) }}">EN</a>
                <a @class(['active' => $locale === 'es']) href="{{ route('pages.show', ['locale' => 'es', 'slug' => $page->slug]) }}">ES</a>
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
