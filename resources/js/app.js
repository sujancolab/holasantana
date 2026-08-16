import './bootstrap';

document.querySelectorAll('[data-mobile-menu-toggle]').forEach((toggle) => {
    const header = toggle.closest('.prime-header, .site-header');
    const menu = header?.querySelector('[data-mobile-menu]');

    if (!header || !menu) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isOpen = header.classList.toggle('is-menu-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    menu.querySelectorAll('a, button').forEach((link) => {
        link.addEventListener('click', () => {
            header.classList.remove('is-menu-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
});

document.querySelectorAll('[data-language-switcher]').forEach((switcher) => {
    const toggle = switcher.querySelector('[data-language-toggle]');

    if (!toggle) {
        return;
    }

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        document.querySelectorAll('.prime-header.is-menu-open, .site-header.is-menu-open').forEach((header) => {
            header.classList.remove('is-menu-open');
            header.querySelector('[data-mobile-menu-toggle]')?.setAttribute('aria-expanded', 'false');
        });
        const isOpen = switcher.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });
});

document.addEventListener('click', () => {
    document.querySelectorAll('[data-language-switcher].is-open').forEach((switcher) => {
        switcher.classList.remove('is-open');
        switcher.querySelector('[data-language-toggle]')?.setAttribute('aria-expanded', 'false');
    });
});

document.querySelectorAll('[data-service-order-modal]').forEach((modal) => {
    const serviceInput = modal.querySelector('[data-service-order-name]');

    const openModal = (serviceName = '') => {
        if (serviceInput && serviceName) {
            serviceInput.value = serviceName;
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        modal.querySelector('input[name="name"]')?.focus();
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    document.querySelectorAll('[data-order-service]').forEach((button) => {
        button.addEventListener('click', () => openModal(button.dataset.orderService || ''));
    });

    modal.querySelectorAll('[data-service-order-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
});

document.querySelectorAll('[data-submit-query-modal]').forEach((modal) => {
    const openButtons = document.querySelectorAll('[data-submit-query-open]');

    const openModal = () => {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        modal.querySelector('input[name="first_name"]')?.focus();
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.prime-header.is-menu-open, .site-header.is-menu-open').forEach((header) => {
                header.classList.remove('is-menu-open');
                header.querySelector('[data-mobile-menu-toggle]')?.setAttribute('aria-expanded', 'false');
            });

            openModal();
        });
    });

    modal.querySelectorAll('[data-submit-query-close]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
});

document.querySelectorAll('[data-slider]').forEach((slider) => {
    const slides = [...slider.querySelectorAll('[data-slide]')];
    const previous = slider.querySelector('[data-slider-prev]');
    const next = slider.querySelector('[data-slider-next]');
    let current = slides.findIndex((slide) => slide.classList.contains('is-active'));

    if (current < 0) {
        current = 0;
    }

    const showSlide = (index) => {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === current);
        });
    };

    previous?.addEventListener('click', () => showSlide(current - 1));
    next?.addEventListener('click', () => showSlide(current + 1));
});

document.querySelectorAll('[data-holiday-home-list]').forEach((listing) => {
    const search = listing.querySelector('[data-holiday-home-search]');
    const cards = [...listing.querySelectorAll('[data-holiday-home-card]')];
    const empty = listing.querySelector('[data-holiday-home-empty]');

    listing.querySelectorAll('[data-holiday-home-more]').forEach((button) => {
        const card = button.closest('[data-holiday-home-card]');
        const description = card?.querySelector('[data-holiday-home-description]');

        if (!card || !description) {
            return;
        }

        button.addEventListener('click', () => {
            const isExpanded = card.classList.toggle('is-description-expanded');
            button.setAttribute('aria-expanded', String(isExpanded));
            button.textContent = isExpanded ? 'Show less' : 'Show more';
        });
    });

    search?.addEventListener('input', () => {
        const query = search.value.trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach((card) => {
            const isVisible = !query || (card.dataset.searchText || '').includes(query);
            card.hidden = !isVisible;
            visibleCount += isVisible ? 1 : 0;
        });

        if (empty) {
            empty.hidden = visibleCount > 0;
        }
    });
});

document.querySelectorAll('[data-cms-editor]').forEach((editor) => {
    const tabs = [...editor.querySelectorAll('[data-cms-tab]')];
    const panels = [...editor.querySelectorAll('[data-cms-panel]')];
    const jsonField = editor.querySelector('[data-cms-json]');
    const blockList = editor.querySelector('[data-cms-block-list]');
    const blockEditor = editor.querySelector('[data-cms-block-editor]');
    const blockModal = editor.querySelector('[data-cms-block-modal]');
    const blockPreview = editor.querySelector('[data-cms-block-preview]');
    const previewLocaleSelect = editor.querySelector('[data-cms-preview-locale]');
    const outline = editor.querySelector('[data-cms-outline]');
    const count = editor.querySelector('[data-cms-block-count]');
    let locales = JSON.parse(editor.dataset.cmsLocales || '{"en":"English","es":"Spanish"}');
    const pagePreview = JSON.parse(editor.dataset.pagePreview || '{}');
    const uploadUrl = editor.dataset.uploadUrl;
    const translateUrl = editor.dataset.translateUrl;
    const translateBlockUrl = editor.dataset.translateBlockUrl;
    const csrfToken = editor.querySelector('input[name="_token"]')?.value;
    let selectedBlock = 0;
    let previewLocale = locales.en ? 'en' : Object.keys(locales)[0] || 'en';

    const blockTemplates = {
        text_section: {
            type: 'text_section',
            heading: { en: 'New text section', es: '' },
            body: { en: 'Write your section content here.', es: '' },
        },
        faq_section: {
            type: 'faq_section',
            heading: { en: 'Frequently asked questions', es: '' },
            body: { en: 'Quick answers about Santana Prime services.', es: '' },
            faqs: [
                {
                    question: { en: 'How can we help?', es: '' },
                    answer: { en: 'Add your answer here.', es: '' },
                },
            ],
        },
        faq_order_form: {
            type: 'faq_order_form',
            heading: { en: 'Submit your order / query', es: 'Envia tu pedido / consulta' },
            services: [
                'Holiday rental cleaning',
                'Private home cleaning',
                'Key holding',
                'Laundry service',
                'Property inspection',
                'Airport transfer',
                'Other',
            ],
            contact_methods: ['Email', 'WhatsApp', 'Telephone'],
        },
        gallery: {
            type: 'gallery',
            images: ['https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=900&q=80'],
        },
        service_section: {
            type: 'service_section',
            heading: { en: 'New service section', es: '' },
            images: [],
            body: { en: 'Describe this service.', es: '' },
        },
        category_products: {
            type: 'category_products',
            heading: { en: 'Products', es: '' },
            products: [
                {
                    name: 'New product',
                    price: '€0.00',
                    image: 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=900&q=80',
                },
            ],
        },
    };

    const blockTypes = {
        text_section: 'Text section',
        panel: 'Panel',
        faq_section: 'FAQ',
        faq_order_form: 'Order/query form',
        open_intro: 'Intro',
        service_section: 'Service section',
        gallery: 'Gallery',
        category_products: 'Products',
        contact: 'Contact',
        slider: 'Slider',
        sample_section: 'Sample section',
        hero_image: 'Hero image',
        wide_image: 'Wide image',
        media_text: 'Media and text',
        split: 'Split layout',
        rental_unit: 'Rental unit',
    };

    const defaultContactItems = () => [
        { label: { en: 'Get in touch with us' }, text: { en: 'Envíenos un correo electrónico' }, url: 'mailto:info@holasantana.com' },
        { label: {}, text: { en: 'Llámenos' }, url: 'tel:+34624229511' },
        { label: { en: 'Whatsapp' }, text: { en: 'Contact us' }, url: 'https://api.whatsapp.com/send?phone=34624229511' },
    ];

    const defaultSocialLinks = () => [
        { icon: 'f', label: 'Facebook', url: '#' },
        { icon: '◎', label: 'Instagram', url: '#' },
        { icon: '▶', label: 'YouTube', url: '#' },
    ];

    const activateTab = (name) => {
        tabs.forEach((tab) => tab.classList.toggle('active', tab.dataset.cmsTab === name));
        panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.cmsPanel === name));
    };

    const parseBlocks = () => {
        if (!jsonField) {
            return [];
        }

        try {
            const parsed = JSON.parse(jsonField.value || '[]');
            jsonField.classList.remove('is-invalid');
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            jsonField.classList.add('is-invalid');
            return [];
        }
    };

    const writeBlocks = (blocks) => {
        if (!jsonField) {
            return;
        }

        jsonField.value = JSON.stringify(blocks, null, 2);
    };

    const syncAndRender = (blocks, nextSelected = selectedBlock) => {
        selectedBlock = Math.max(0, Math.min(nextSelected, Math.max(blocks.length - 1, 0)));
        writeBlocks(blocks);
        renderBlocks();
    };

    const openBlockModal = () => {
        if (!blockModal) {
            return;
        }

        blockModal.classList.add('is-open');
        blockModal.setAttribute('aria-hidden', 'false');
        blockModal.querySelector('[data-cms-block-editor] input, [data-cms-block-editor] textarea, [data-cms-block-editor] select')?.focus();
    };

    const closeBlockModal = () => {
        if (!blockModal) {
            return;
        }

        blockModal.classList.remove('is-open');
        blockModal.setAttribute('aria-hidden', 'true');
    };

    const firstImage = (block) => {
        if (block.image) {
            return block.image;
        }

        if (Array.isArray(block.images) && block.images[0]) {
            return block.images[0];
        }

        if (Array.isArray(block.products) && block.products[0]?.image) {
            return block.products[0].image;
        }

        if (Array.isArray(block.videos) && block.videos[0]?.poster) {
            return block.videos[0].poster;
        }

        return '';
    };

    const getLocalized = (value, locale) => {
        if (value && typeof value === 'object' && !Array.isArray(value)) {
            return value[locale] || '';
        }

        return locale === 'en' ? (value || '') : '';
    };

    const setLocalized = (block, key, locale, value) => {
        if (!block[key] || typeof block[key] !== 'object' || Array.isArray(block[key])) {
            block[key] = {};
        }

        block[key][locale] = value;
    };

    const localizedBlockValue = (block, key, locale = previewLocale) => {
        return getLocalized(block?.[key], locale) || getLocalized(block?.[key], 'en');
    };

    const listToText = (value) => {
        if (Array.isArray(value)) {
            return value.join('\n');
        }

        return String(value || '');
    };

    const textToList = (value) => {
        return String(value || '').split('\n').map((line) => line.trim()).filter(Boolean);
    };

    const getLocalizedList = (block, key, locale = previewLocale) => {
        const value = block?.[key];

        if (Array.isArray(value)) {
            return value;
        }

        if (value && typeof value === 'object') {
            const localized = value[locale] || value.en || [];
            return Array.isArray(localized) ? localized : textToList(localized);
        }

        return [];
    };

    const setLocalizedList = (block, key, locale, value) => {
        const lines = textToList(value);

        if (Array.isArray(block[key])) {
            block[key] = { en: block[key] };
        }

        if (!block[key] || typeof block[key] !== 'object') {
            block[key] = {};
        }

        block[key][locale] = lines;
    };

    const localizedPageValue = (key, locale = previewLocale) => {
        return getLocalized(pagePreview?.[key], locale) || getLocalized(pagePreview?.[key], 'en');
    };

    const createEl = (tag, className, text = '') => {
        const el = document.createElement(tag);
        if (className) {
            el.className = className;
        }
        if (text) {
            el.textContent = text;
        }
        return el;
    };

    const appendMultiline = (container, text) => {
        String(text || '').split('\n').forEach((line, index) => {
            if (index) {
                container.append(document.createElement('br'));
            }
            container.append(document.createTextNode(line));
        });
    };

    const imageEl = (src, className = '') => {
        const img = document.createElement('img');
        img.src = src;
        img.alt = '';
        img.loading = 'lazy';
        img.decoding = 'async';
        if (className) {
            img.className = className;
        }
        return img;
    };

    const uploadImages = async (files, status) => {
        if (!uploadUrl || !csrfToken || !files.length) {
            return [];
        }

        const urls = [];
        status.textContent = `Uploading ${files.length} ${files.length === 1 ? 'image' : 'images'}...`;

        for (const file of files) {
            const data = new FormData();
            data.append('image', file);

            const response = await fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
                body: data,
            });

            if (!response.ok) {
                throw new Error('Upload failed');
            }

            const payload = await response.json();
            urls.push(payload.url);
        }

        status.textContent = 'Upload complete';
        setTimeout(() => {
            status.textContent = '';
        }, 2200);

        return urls;
    };

    const translateText = async ({ text, targetLocale, field }) => {
        if (!translateUrl || !csrfToken) {
            throw new Error('Translation is not configured.');
        }

        const response = await fetch(translateUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text,
                source_locale: 'en',
                target_locale: targetLocale,
                field,
            }),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(payload.message || 'Translation failed.');
        }

        return payload.translation || '';
    };

    const translateBlock = async ({ fields, targetLocales }) => {
        if (!translateBlockUrl || !csrfToken) {
            throw new Error('Translation is not configured.');
        }

        const response = await fetch(translateBlockUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                source_locale: 'en',
                target_locales: targetLocales,
                fields,
            }),
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(payload.message || 'Translation failed.');
        }

        return payload.translations || {};
    };

    const field = (labelText, value, onInput, options = {}) => {
        const label = createEl('label', options.wide ? 'wide' : '');
        label.append(document.createTextNode(labelText));
        const control = document.createElement(options.multiline ? 'textarea' : 'input');
        if (options.multiline) {
            control.rows = options.rows || 4;
        }
        control.value = value || '';
        control.placeholder = options.placeholder || '';
        control.addEventListener('input', () => onInput(control.value));
        if (options.onChange) {
            control.addEventListener('change', options.onChange);
        }
        label.append(control);
        return label;
    };

    const imageUploadControl = ({ multiple = false, onUploaded }) => {
        const wrap = createEl('div', 'cms-upload-control');
        const label = createEl('label', 'cms-upload-button', multiple ? 'Upload images' : 'Upload image');
        const input = document.createElement('input');
        const status = createEl('span', 'cms-upload-status');
        input.type = 'file';
        input.accept = 'image/*';
        input.multiple = multiple;
        label.append(input);
        wrap.append(label, status);

        input.addEventListener('change', async () => {
            const files = [...input.files];
            if (!files.length) {
                return;
            }

            try {
                const urls = await uploadImages(files, status);
                onUploaded(urls);
            } catch {
                status.textContent = 'Upload failed';
            } finally {
                input.value = '';
            }
        });

        return wrap;
    };

    const renderLocaleFields = (container, block, key, labelText, options = {}) => {
        const group = createEl('div', 'cms-locale-grid');
        Object.entries(locales).forEach(([locale, localeLabel]) => {
            const localeField = field(`${localeLabel} ${labelText}`, getLocalized(block[key], locale), (value) => {
                const blocks = parseBlocks();
                setLocalized(blocks[selectedBlock], key, locale, value);
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, options);

            if (locale !== 'en') {
                const control = localeField.querySelector('input, textarea');
                const actions = createEl('div', 'cms-translate-row');
                const button = createEl('button', 'cms-translate-button', `Translate from English`);
                const status = createEl('span', 'cms-translate-status');
                button.type = 'button';
                button.addEventListener('click', async () => {
                    const blocks = parseBlocks();
                    const sourceText = getLocalized(blocks[selectedBlock]?.[key], 'en').trim();

                    if (!sourceText) {
                        status.textContent = 'Add English text first';
                        return;
                    }

                    button.disabled = true;
                    status.textContent = 'Translating...';

                    try {
                        const translated = await translateText({
                            text: sourceText,
                            targetLocale: locale,
                            field: labelText,
                        });
                        const latest = parseBlocks();
                        setLocalized(latest[selectedBlock], key, locale, translated);
                        writeBlocks(latest);
                        control.value = translated;
                        renderBlockPreview(latest[selectedBlock]);
                        status.textContent = 'Translated';
                    } catch (error) {
                        status.textContent = error.message || 'Translation failed';
                    } finally {
                        button.disabled = false;
                    }
                });
                actions.append(button, status);
                localeField.append(actions);
            }

            group.append(localeField);
        });
        container.append(group);
    };

    const blockTitle = (block, index) => {
        return block.heading?.en
            || block.title
            || block.products?.[0]?.name
            || `${block.type || 'content'} block ${index + 1}`;
    };

    const blockSummary = (block) => {
        if (Array.isArray(block.products)) {
            return `${block.products.length} products`;
        }

        if (Array.isArray(block.images)) {
            return `${block.images.length} images`;
        }

        if (Array.isArray(block.videos)) {
            return `${block.videos.length} videos`;
        }

        if (Array.isArray(block.faqs)) {
            return `${block.faqs.length} questions`;
        }

        return block.body?.en || block.footer?.en || 'Ready to edit in JSON';
    };

    const renderPreviewActions = (container, actions = []) => {
        if (!Array.isArray(actions) || !actions.length) {
            return;
        }

        const row = createEl('div', 'prime-button-row');
        actions.forEach((action) => {
            const label = getLocalized(action.label, previewLocale) || getLocalized(action.label, 'en');
            if (!label) {
                return;
            }
            const link = createEl('a', `prime-button ${action.variant ? `is-${action.variant}` : (action.class || '')}`, label);
            link.href = action.url || '#';
            row.append(link);
        });
        container.append(row);
    };

    const renderBlockPreview = (block) => {
        if (!blockPreview) {
            return;
        }

        blockPreview.innerHTML = '';
        blockPreview.className = `cms-block-preview-surface prime-site page-${String(pagePreview.slug || 'preview').replace(/[\/_]/g, '-')}`;

        if (!block) {
            blockPreview.append(createEl('p', 'hint', 'Select a block to preview.'));
            return;
        }

        const type = block.type || 'text_section';
        const heading = localizedBlockValue(block, 'heading');
        const body = localizedBlockValue(block, 'body');
        const footer = localizedBlockValue(block, 'footer');
        const items = getLocalizedList(block, 'items');
        let section;

        if (type === 'hero_image') {
            section = createEl('section', 'prime-hero');
            if (block.image) {
                section.style.setProperty('--hero-image', `url('${block.image}')`);
            }
            section.append(
                createEl('h1', '', localizedPageValue('hero_title') || heading || blockTitle(block, selectedBlock)),
                createEl('p', '', localizedPageValue('hero_subtitle') || body),
            );
        } else if (type === 'hero_panel') {
            section = createEl('section', 'prime-panel prime-page-hero');
            section.append(
                createEl('h1', '', localizedPageValue('hero_title') || heading),
                createEl('p', '', localizedPageValue('hero_subtitle') || body),
            );
        } else if (type === 'wide_image') {
            section = createEl('section', 'prime-wide-image');
            if (block.image) {
                section.append(imageEl(block.image));
            }
        } else if (type === 'gallery') {
            section = createEl('section', `prime-gallery is-count-${(block.images || []).length}`);
            (block.images || []).forEach((image) => section.append(imageEl(image)));
        } else if (type === 'service_section') {
            section = createEl('section', 'prime-open-section');
            if (heading) section.append(createEl('h2', '', heading));
            if (Array.isArray(block.images) && block.images.length) {
                const images = createEl('div', `prime-service-images is-count-${block.images.length}`);
                block.images.forEach((image) => images.append(imageEl(image)));
                section.append(images);
            }
            if (body) {
                const copy = createEl('div', 'prime-open-copy');
                appendMultiline(copy, body);
                section.append(copy);
            }
        } else if (type === 'category_products') {
            section = createEl('section', 'prime-category');
            if (heading) section.append(createEl('h1', '', heading));
            const grid = createEl('div', 'prime-products');
            (block.products || []).forEach((product) => {
                const card = createEl('article', 'prime-product-card');
                const media = createEl('div', 'prime-product-image');
                if (product.image) media.append(imageEl(product.image));
                const price = createEl('p', 'prime-product-price');
                if (product.sale_price && product.price) {
                    price.append(createEl('span', 'prime-price-old', product.price));
                }
                if (product.sale_price || product.price) {
                    price.append(createEl('span', '', product.sale_price || product.price));
                }
                const button = createEl('button', '', 'Order It');
                button.type = 'button';
                card.append(media, createEl('h2', '', product.name || 'Product'));
                if (price.childNodes.length) {
                    card.append(price);
                }
                card.append(button);
                grid.append(card);
            });
            section.append(grid);
        } else if (type === 'faq_section') {
            section = createEl('section', 'prime-faq-section');
            const intro = createEl('div', 'prime-faq-intro');
            if (heading) intro.append(createEl('h1', '', heading));
            if (body) {
                const copy = createEl('p', '', '');
                appendMultiline(copy, body);
                intro.append(copy);
            }
            section.append(intro);

            const list = createEl('div', 'prime-faq-list');
            (block.faqs || []).forEach((faq, faqIndex) => {
                const item = createEl('details', 'prime-faq-item');
                if (faqIndex === 0) {
                    item.open = true;
                }
                item.append(
                    createEl('summary', '', getLocalized(faq.question, previewLocale) || getLocalized(faq.question, 'en') || `Question ${faqIndex + 1}`),
                );
                const answer = createEl('div', 'prime-faq-answer');
                appendMultiline(answer, getLocalized(faq.answer, previewLocale) || getLocalized(faq.answer, 'en'));
                item.append(answer);
                list.append(item);
            });
            section.append(list);
        } else if (type === 'faq_order_form') {
            section = createEl('section', 'faq-order-section');
            if (heading) section.append(createEl('h1', '', heading));
            const form = createEl('form', 'faq-order-form');
            [
                'First name *',
                'Last name *',
                'Telephone number *',
                'Email',
                'Property address',
                'Ordering date',
                'Service area',
                'Service date',
                'Approximate service time',
                'Your message *',
            ].forEach((label) => form.append(createEl('label', '', label)));
            form.append(createEl('button', '', 'Submit query'));
            section.append(form);
        } else if (type === 'contact') {
            section = createEl('section', 'prime-contact');
            if (block.left_image) section.append(imageEl(block.left_image));
            const panel = createEl('div', 'prime-panel');
            if (heading) {
                const title = createEl('h2');
                appendMultiline(title, heading);
                panel.append(title);
            }
            const grid = createEl('div', 'contact-grid');
            (block.contact_items || defaultContactItems()).forEach((item) => {
                const label = getLocalized(item.label, previewLocale) || getLocalized(item.label, 'en');
                const text = getLocalized(item.text, previewLocale) || getLocalized(item.text, 'en');
                if (label) grid.append(createEl('strong', '', label));
                if (text) {
                    const link = createEl('a', '', text);
                    link.href = item.url || '#';
                    grid.append(link);
                }
            });
            const socialHeading = getLocalized(block.social_heading, previewLocale) || getLocalized(block.social_heading, 'en') || 'Follow us at';
            if (socialHeading) grid.append(createEl('strong', '', socialHeading));
            const socialRow = createEl('div', 'social-row');
            (block.social_links || defaultSocialLinks()).forEach((social) => {
                const socialLink = social.url ? createEl('a', '', social.icon || social.label || '') : createEl('span', '', social.icon || social.label || '');
                if (social.url) socialLink.href = social.url;
                socialRow.append(socialLink);
            });
            grid.append(socialRow);
            panel.append(grid);
            section.append(panel);
            if (block.right_image) section.append(imageEl(block.right_image));
        } else if (type === 'rental_unit') {
            section = createEl('section', 'prime-rental-unit');
            if (heading) section.append(createEl('h2', '', heading));
            if (Array.isArray(block.images) && block.images.length) {
                const gallery = createEl('div', `prime-rental-gallery is-count-${block.images.length}`);
                block.images.forEach((image) => gallery.append(imageEl(image)));
                section.append(gallery);
            }
            if (body) {
                const copy = createEl('div', 'prime-open-copy');
                appendMultiline(copy, body);
                section.append(copy);
            }
            renderPreviewActions(section, block.actions);
        } else if (type === 'media_text') {
            section = createEl('section', `prime-panel prime-media-text ${block.reverse ? 'is-reverse' : ''}`);
            if (block.image) section.append(imageEl(block.image));
            const content = document.createElement('div');
            if (heading) content.append(createEl('h2', '', heading));
            if (body) {
                const copy = createEl('div', 'prime-copy');
                appendMultiline(copy, body);
                content.append(copy);
            }
            if (items.length) {
                const list = createEl('ul', 'prime-checks');
                items.forEach((item) => list.append(createEl('li', '', item)));
                content.append(list);
            }
            section.append(content);
        } else if (type === 'split') {
            section = createEl('section', 'prime-panel prime-split');
            const content = document.createElement('div');
            if (heading) content.append(createEl('h2', '', heading));
            if (body) {
                const copy = createEl('div', 'prime-copy');
                appendMultiline(copy, body);
                content.append(copy);
            }
            if (items.length) {
                const list = createEl('ul', 'prime-checks');
                items.forEach((item) => list.append(createEl('li', '', item)));
                content.append(list);
            }
            renderPreviewActions(content, block.actions);
            section.append(content);
            if (block.image) section.append(imageEl(block.image));
        } else {
            section = createEl('section', `prime-open-section prime-text-section ${block.class || ''}`);
            if (heading) section.append(createEl('h2', '', heading));
            if (body) {
                const copy = createEl('div', 'prime-open-copy');
                appendMultiline(copy, body);
                section.append(copy);
            }
            if (items.length) {
                const list = createEl('ul', 'prime-checks');
                items.forEach((item) => list.append(createEl('li', '', item)));
                section.append(list);
            }
            if (footer) {
                const foot = createEl('div', 'prime-open-footer');
                appendMultiline(foot, footer);
                section.append(foot);
            }
            renderPreviewActions(section, block.actions);
        }

        blockPreview.append(section || createEl('p', 'hint', 'Preview is not available for this block type.'));
    };

    const renderImagesEditor = (container, block) => {
        if (block.type === 'contact') {
            return;
        }

        const hasImages = Array.isArray(block.images) || ['gallery', 'service_section', 'rental_unit'].includes(block.type);

        if (hasImages) {
            const imageField = field('Image URLs, one per line', (block.images || []).join('\n'), (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].images = value.split('\n').map((line) => line.trim()).filter(Boolean);
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { wide: true, multiline: true, rows: 5, placeholder: 'https://...', onChange: renderBlocks });
            imageField.append(imageUploadControl({
                multiple: true,
                onUploaded: (urls) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].images ??= [];
                    blocks[selectedBlock].images.push(...urls);
                    syncAndRender(blocks);
                },
            }));
            container.append(imageField);
        } else {
            const imageField = field('Image URL', block.image || '', (value) => {
                const blocks = parseBlocks();
                if (value.trim()) {
                    blocks[selectedBlock].image = value.trim();
                } else {
                    delete blocks[selectedBlock].image;
                }
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { wide: true, placeholder: 'https://...', onChange: renderBlocks });
            imageField.append(imageUploadControl({
                onUploaded: ([url]) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].image = url;
                    syncAndRender(blocks);
                },
            }));
            container.append(imageField);
        }
    };

    const renderActionsEditor = (container, block) => {
        const actions = Array.isArray(block.actions) ? block.actions : [];
        const card = createEl('div', 'cms-subeditor');
        const head = createEl('div', 'cms-subeditor-head');
        head.append(createEl('h4', '', 'Buttons'));
        const add = createEl('button', 'button ghost', 'Add button');
        add.type = 'button';
        add.addEventListener('click', () => {
            const blocks = parseBlocks();
            blocks[selectedBlock].actions ??= [];
            blocks[selectedBlock].actions.push({ label: { en: 'New button' }, url: '#' });
            syncAndRender(blocks);
        });
        head.append(add);
        card.append(head);

        if (!actions.length) {
            card.append(createEl('p', 'hint', 'No buttons yet.'));
        }

        actions.forEach((action, actionIndex) => {
            const actionCard = createEl('div', 'cms-action-row');
            const actionHead = createEl('div', 'cms-subeditor-head');
            actionHead.append(createEl('h4', '', `Button ${actionIndex + 1}`));

            const actionControls = createEl('div', 'cms-block-controls');
            [
                ['Up', () => {
                    const blocks = parseBlocks();
                    if (actionIndex === 0) return;
                    [blocks[selectedBlock].actions[actionIndex - 1], blocks[selectedBlock].actions[actionIndex]] = [blocks[selectedBlock].actions[actionIndex], blocks[selectedBlock].actions[actionIndex - 1]];
                    syncAndRender(blocks);
                }],
                ['Down', () => {
                    const blocks = parseBlocks();
                    if (actionIndex >= blocks[selectedBlock].actions.length - 1) return;
                    [blocks[selectedBlock].actions[actionIndex + 1], blocks[selectedBlock].actions[actionIndex]] = [blocks[selectedBlock].actions[actionIndex], blocks[selectedBlock].actions[actionIndex + 1]];
                    syncAndRender(blocks);
                }],
                ['Delete', () => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].actions.splice(actionIndex, 1);
                    syncAndRender(blocks);
                }],
            ].forEach(([label, handler]) => {
                const button = createEl('button', `cms-icon-button ${label === 'Delete' ? 'danger' : ''}`, label);
                button.type = 'button';
                button.addEventListener('click', handler);
                actionControls.append(button);
            });
            actionHead.append(actionControls);
            actionCard.append(actionHead);

            Object.entries(locales).forEach(([locale, localeLabel]) => {
                actionCard.append(field(`${localeLabel} label`, getLocalized(action.label, locale), (value) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].actions ??= [];
                    blocks[selectedBlock].actions[actionIndex] ??= {};
                    blocks[selectedBlock].actions[actionIndex].label ??= {};
                    if (typeof blocks[selectedBlock].actions[actionIndex].label === 'string') {
                        blocks[selectedBlock].actions[actionIndex].label = { en: blocks[selectedBlock].actions[actionIndex].label };
                    }
                    blocks[selectedBlock].actions[actionIndex].label[locale] = value;
                    writeBlocks(blocks);
                    renderBlockPreview(blocks[selectedBlock]);
                }));
            });

            actionCard.append(field('URL', action.url || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].actions ??= [];
                blocks[selectedBlock].actions[actionIndex] ??= {};
                blocks[selectedBlock].actions[actionIndex].url = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { placeholder: '/en/contact' }));

            actionCard.append(field('Style variant', action.variant || action.class || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].actions ??= [];
                blocks[selectedBlock].actions[actionIndex] ??= {};
                const cleanValue = value.trim();

                if (cleanValue) {
                    blocks[selectedBlock].actions[actionIndex].variant = cleanValue;
                } else {
                    delete blocks[selectedBlock].actions[actionIndex].variant;
                    delete blocks[selectedBlock].actions[actionIndex].class;
                }

                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { placeholder: 'secondary' }));

            card.append(actionCard);
        });

        container.append(card);
    };

    const renderItemsEditor = (container, block) => {
        const hasItems = Array.isArray(block.items)
            || (block.items && typeof block.items === 'object')
            || ['panel', 'text_section', 'split', 'media_text'].includes(block.type || 'panel');

        if (!hasItems) {
            return;
        }

        const card = createEl('div', 'cms-subeditor');
        const head = createEl('div', 'cms-subeditor-head');
        head.append(createEl('h4', '', 'Selection list'));
        card.append(head);

        Object.entries(locales).forEach(([locale, localeLabel]) => {
            const currentValue = Array.isArray(block.items)
                ? (locale === 'en' ? block.items : [])
                : (block.items?.[locale] || []);

            const listField = field(`${localeLabel} items, one per line`, listToText(currentValue), (value) => {
                const blocks = parseBlocks();
                setLocalizedList(blocks[selectedBlock], 'items', locale, value);
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { wide: true, multiline: true, rows: 5 });

            if (locale !== 'en') {
                const control = listField.querySelector('textarea');
                const actions = createEl('div', 'cms-translate-row');
                const button = createEl('button', 'cms-translate-button', 'Translate from English');
                const status = createEl('span', 'cms-translate-status');
                button.type = 'button';
                button.addEventListener('click', async () => {
                    const blocks = parseBlocks();
                    const sourceText = listToText(getLocalizedList(blocks[selectedBlock], 'items', 'en')).trim();

                    if (!sourceText) {
                        status.textContent = 'Add English items first';
                        return;
                    }

                    button.disabled = true;
                    status.textContent = 'Translating...';

                    try {
                        const translated = await translateText({
                            text: sourceText,
                            targetLocale: locale,
                            field: 'selection list',
                        });
                        const latest = parseBlocks();
                        setLocalizedList(latest[selectedBlock], 'items', locale, translated);
                        writeBlocks(latest);
                        control.value = listToText(getLocalizedList(latest[selectedBlock], 'items', locale));
                        renderBlockPreview(latest[selectedBlock]);
                        status.textContent = 'Translated';
                    } catch (error) {
                        status.textContent = error.message || 'Translation failed';
                    } finally {
                        button.disabled = false;
                    }
                });
                actions.append(button, status);
                listField.append(actions);
            }

            card.append(listField);
        });

        container.append(card);
    };

    const renderFaqEditor = (container, block) => {
        if (!Array.isArray(block.faqs) && block.type !== 'faq_section') {
            return;
        }

        const card = createEl('div', 'cms-subeditor');
        const head = createEl('div', 'cms-subeditor-head');
        head.append(createEl('h4', '', 'FAQ questions'));
        const add = createEl('button', 'button ghost', 'Add question');
        add.type = 'button';
        add.addEventListener('click', () => {
            const blocks = parseBlocks();
            blocks[selectedBlock].faqs ??= [];
            blocks[selectedBlock].faqs.push({
                question: { en: 'New question' },
                answer: { en: 'New answer' },
            });
            syncAndRender(blocks);
        });
        head.append(add);
        card.append(head);

        if (!Array.isArray(block.faqs) || !block.faqs.length) {
            card.append(createEl('p', 'hint', 'No FAQ questions yet.'));
        }

        (block.faqs || []).forEach((faq, faqIndex) => {
            const faqCard = createEl('div', 'cms-action-row');
            const faqHead = createEl('div', 'cms-subeditor-head');
            faqHead.append(createEl('h4', '', `Question ${faqIndex + 1}`));

            const faqControls = createEl('div', 'cms-block-controls');
            [
                ['Up', () => {
                    const blocks = parseBlocks();
                    if (faqIndex === 0) return;
                    [blocks[selectedBlock].faqs[faqIndex - 1], blocks[selectedBlock].faqs[faqIndex]] = [blocks[selectedBlock].faqs[faqIndex], blocks[selectedBlock].faqs[faqIndex - 1]];
                    syncAndRender(blocks);
                }],
                ['Down', () => {
                    const blocks = parseBlocks();
                    if (faqIndex >= blocks[selectedBlock].faqs.length - 1) return;
                    [blocks[selectedBlock].faqs[faqIndex + 1], blocks[selectedBlock].faqs[faqIndex]] = [blocks[selectedBlock].faqs[faqIndex], blocks[selectedBlock].faqs[faqIndex + 1]];
                    syncAndRender(blocks);
                }],
                ['Delete', () => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].faqs.splice(faqIndex, 1);
                    syncAndRender(blocks);
                }],
            ].forEach(([label, handler]) => {
                const button = createEl('button', `cms-icon-button ${label === 'Delete' ? 'danger' : ''}`, label);
                button.type = 'button';
                button.addEventListener('click', handler);
                faqControls.append(button);
            });
            faqHead.append(faqControls);
            faqCard.append(faqHead);

            Object.entries(locales).forEach(([locale, localeLabel]) => {
                faqCard.append(field(`${localeLabel} question`, getLocalized(faq.question, locale), (value) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].faqs ??= [];
                    blocks[selectedBlock].faqs[faqIndex] ??= {};
                    blocks[selectedBlock].faqs[faqIndex].question ??= {};
                    blocks[selectedBlock].faqs[faqIndex].question[locale] = value;
                    writeBlocks(blocks);
                    renderBlockPreview(blocks[selectedBlock]);
                }));
                faqCard.append(field(`${localeLabel} answer`, getLocalized(faq.answer, locale), (value) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].faqs ??= [];
                    blocks[selectedBlock].faqs[faqIndex] ??= {};
                    blocks[selectedBlock].faqs[faqIndex].answer ??= {};
                    blocks[selectedBlock].faqs[faqIndex].answer[locale] = value;
                    writeBlocks(blocks);
                    renderBlockPreview(blocks[selectedBlock]);
                }, { wide: true, multiline: true, rows: 3 }));
            });

            card.append(faqCard);
        });

        container.append(card);
    };

    const renderContactEditor = (container, block) => {
        if (block.type !== 'contact') {
            return;
        }

        const imageCard = createEl('div', 'cms-subeditor');
        const imageHead = createEl('div', 'cms-subeditor-head');
        imageHead.append(createEl('h4', '', 'Contact images'));
        imageCard.append(imageHead);

        [
            ['Left image URL', 'left_image'],
            ['Right image URL', 'right_image'],
        ].forEach(([labelText, key]) => {
            const imageField = field(labelText, block[key] || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock][key] = value.trim();
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { wide: true, placeholder: 'https://...', onChange: renderBlocks });
            imageField.append(imageUploadControl({
                onUploaded: ([url]) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock][key] = url;
                    syncAndRender(blocks);
                },
            }));
            imageCard.append(imageField);
        });
        container.append(imageCard);

        const contactCard = createEl('div', 'cms-subeditor');
        const contactHead = createEl('div', 'cms-subeditor-head');
        contactHead.append(createEl('h4', '', 'Contact links'));
        const addContact = createEl('button', 'button ghost', 'Add link');
        addContact.type = 'button';
        addContact.addEventListener('click', () => {
            const blocks = parseBlocks();
            blocks[selectedBlock].contact_items ??= defaultContactItems();
            blocks[selectedBlock].contact_items.push({ label: { en: '' }, text: { en: 'New link' }, url: '#' });
            syncAndRender(blocks);
        });
        contactHead.append(addContact);
        contactCard.append(contactHead);

        (block.contact_items || defaultContactItems()).forEach((item, itemIndex) => {
            const row = createEl('div', 'cms-action-row');
            const rowHead = createEl('div', 'cms-subeditor-head');
            rowHead.append(createEl('h4', '', `Contact link ${itemIndex + 1}`));
            const remove = createEl('button', 'cms-icon-button danger', 'Delete');
            remove.type = 'button';
            remove.addEventListener('click', () => {
                const blocks = parseBlocks();
                blocks[selectedBlock].contact_items ??= defaultContactItems();
                blocks[selectedBlock].contact_items.splice(itemIndex, 1);
                syncAndRender(blocks);
            });
            rowHead.append(remove);
            row.append(rowHead);

            Object.entries(locales).forEach(([locale, localeLabel]) => {
                row.append(field(`${localeLabel} heading`, getLocalized(item.label, locale), (value) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].contact_items ??= defaultContactItems();
                    blocks[selectedBlock].contact_items[itemIndex].label ??= {};
                    blocks[selectedBlock].contact_items[itemIndex].label[locale] = value;
                    writeBlocks(blocks);
                    renderBlockPreview(blocks[selectedBlock]);
                }));
                row.append(field(`${localeLabel} link text`, getLocalized(item.text, locale), (value) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].contact_items ??= defaultContactItems();
                    blocks[selectedBlock].contact_items[itemIndex].text ??= {};
                    blocks[selectedBlock].contact_items[itemIndex].text[locale] = value;
                    writeBlocks(blocks);
                    renderBlockPreview(blocks[selectedBlock]);
                }));
            });

            row.append(field('URL', item.url || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].contact_items ??= defaultContactItems();
                blocks[selectedBlock].contact_items[itemIndex].url = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { wide: true, placeholder: 'mailto:info@example.com' }));
            contactCard.append(row);
        });
        container.append(contactCard);

        const socialCard = createEl('div', 'cms-subeditor');
        const socialHead = createEl('div', 'cms-subeditor-head');
        socialHead.append(createEl('h4', '', 'Social links'));
        const addSocial = createEl('button', 'button ghost', 'Add social');
        addSocial.type = 'button';
        addSocial.addEventListener('click', () => {
            const blocks = parseBlocks();
            blocks[selectedBlock].social_links ??= defaultSocialLinks();
            blocks[selectedBlock].social_links.push({ icon: '•', label: 'Social', url: '#' });
            syncAndRender(blocks);
        });
        socialHead.append(addSocial);
        socialCard.append(socialHead);

        Object.entries(locales).forEach(([locale, localeLabel]) => {
            socialCard.append(field(`${localeLabel} social heading`, getLocalized(block.social_heading, locale), (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].social_heading ??= {};
                blocks[selectedBlock].social_heading[locale] = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }));
        });

        (block.social_links || defaultSocialLinks()).forEach((social, socialIndex) => {
            const row = createEl('div', 'cms-action-row');
            row.append(field('Icon text', social.icon || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].social_links ??= defaultSocialLinks();
                blocks[selectedBlock].social_links[socialIndex].icon = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }));
            row.append(field('Accessible label', social.label || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].social_links ??= defaultSocialLinks();
                blocks[selectedBlock].social_links[socialIndex].label = value;
                writeBlocks(blocks);
            }));
            row.append(field('URL', social.url || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].social_links ??= defaultSocialLinks();
                blocks[selectedBlock].social_links[socialIndex].url = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { wide: true, placeholder: 'https://...' }));
            const remove = createEl('button', 'cms-icon-button danger', 'Delete social');
            remove.type = 'button';
            remove.addEventListener('click', () => {
                const blocks = parseBlocks();
                blocks[selectedBlock].social_links ??= defaultSocialLinks();
                blocks[selectedBlock].social_links.splice(socialIndex, 1);
                syncAndRender(blocks);
            });
            row.append(remove);
            socialCard.append(row);
        });
        container.append(socialCard);
    };

    const renderProductsEditor = (container, block) => {
        if (!Array.isArray(block.products) && block.type !== 'category_products') {
            return;
        }

        const wrap = createEl('div', 'cms-product-editor');
        const head = createEl('div', 'cms-subeditor-head');
        head.append(createEl('h4', '', 'Products'));
        const add = createEl('button', 'button ghost', 'Add product');
        add.type = 'button';
        add.addEventListener('click', () => {
            const blocks = parseBlocks();
            blocks[selectedBlock].products ??= [];
            blocks[selectedBlock].products.push({ name: 'New product', price: '€0.00', sale_price: '', image: '' });
            syncAndRender(blocks);
        });
        head.append(add);
        wrap.append(head);

        (block.products || []).forEach((product, productIndex) => {
            const productCard = createEl('div', 'cms-product-row');
            const media = product.image ? createEl('img', 'cms-product-thumb') : createEl('span', 'cms-product-thumb');
            if (product.image) {
                media.src = product.image;
                media.alt = '';
            }
            productCard.append(media);

            const fields = createEl('div', 'cms-product-fields');
            fields.append(field('Name', product.name || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].products[productIndex].name = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }));
            fields.append(field('Price', product.price || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].products[productIndex].price = value;
                writeBlocks(blocks);
            }));
            fields.append(field('Sale price', product.sale_price || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].products[productIndex].sale_price = value;
                writeBlocks(blocks);
            }));
            const imageField = field('Image URL', product.image || '', (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].products[productIndex].image = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }, { wide: true, onChange: renderBlocks });
            imageField.append(imageUploadControl({
                onUploaded: ([url]) => {
                    const blocks = parseBlocks();
                    blocks[selectedBlock].products[productIndex].image = url;
                    syncAndRender(blocks);
                },
            }));
            fields.append(imageField);
            productCard.append(fields);

            const remove = createEl('button', 'cms-icon-button danger', 'Delete');
            remove.type = 'button';
            remove.addEventListener('click', () => {
                const blocks = parseBlocks();
                blocks[selectedBlock].products.splice(productIndex, 1);
                syncAndRender(blocks);
            });
            productCard.append(remove);
            wrap.append(productCard);
        });

        container.append(wrap);
    };

    const renderSelectedBlockEditor = (blocks) => {
        if (!blockEditor) {
            return;
        }

        blockEditor.innerHTML = '';

        if (!blocks.length) {
            blockEditor.append(createEl('p', 'hint', 'Add a block to start editing page content.'));
            return;
        }

        const block = blocks[selectedBlock] || blocks[0];
        const title = createEl('div', 'cms-card-head');
        title.append(createEl('h3', '', blockTitle(block, selectedBlock)));

        const typeSelect = document.createElement('select');
        Object.entries(blockTypes).forEach(([value, label]) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            option.selected = (block.type || 'text_section') === value;
            typeSelect.append(option);
        });
        typeSelect.addEventListener('change', () => {
            const current = parseBlocks();
            current[selectedBlock].type = typeSelect.value;
            if (typeSelect.value === 'category_products') {
                current[selectedBlock].products ??= [];
            }
            if (['gallery', 'service_section', 'rental_unit'].includes(typeSelect.value)) {
                current[selectedBlock].images ??= [];
            }
            syncAndRender(current);
        });
        title.append(typeSelect);
        blockEditor.append(title);

        const translatePanel = createEl('div', 'cms-block-translate-panel');
        const translateAll = createEl('button', 'button ghost', 'Translate all from English');
        const translateStatus = createEl('span', 'cms-translate-status');
        translateAll.type = 'button';
        translateAll.addEventListener('click', async () => {
            const blocks = parseBlocks();
            const currentBlock = blocks[selectedBlock];
            const fields = {
                heading: getLocalized(currentBlock?.heading, 'en').trim(),
                body: getLocalized(currentBlock?.body, 'en').trim(),
                footer: getLocalized(currentBlock?.footer, 'en').trim(),
                items: listToText(getLocalizedList(currentBlock, 'items', 'en')).trim(),
            };
            const hasFields = Object.values(fields).some(Boolean);
            const targetLocales = Object.keys(locales).filter((locale) => locale !== 'en');

            if (!hasFields) {
                translateStatus.textContent = 'Add English heading, body, footer, or selection list first';
                return;
            }

            if (!targetLocales.length) {
                translateStatus.textContent = 'Add another language first';
                return;
            }

            translateAll.disabled = true;
            translateStatus.textContent = 'Translating section...';

            try {
                const translations = await translateBlock({ fields, targetLocales });
                const latest = parseBlocks();

                Object.entries(translations).forEach(([locale, translatedFields]) => {
                    if (!targetLocales.includes(locale) || !translatedFields || typeof translatedFields !== 'object') {
                        return;
                    }

                    ['heading', 'body', 'footer'].forEach((key) => {
                        if (fields[key] && typeof translatedFields[key] === 'string') {
                            setLocalized(latest[selectedBlock], key, locale, translatedFields[key]);
                        }
                    });

                    if (fields.items && typeof translatedFields.items === 'string') {
                        setLocalizedList(latest[selectedBlock], 'items', locale, translatedFields.items);
                    }
                });

                writeBlocks(latest);
                renderBlocks();
                blockEditor.querySelector('.cms-block-translate-panel .cms-translate-status').textContent = 'Section translated';
            } catch (error) {
                translateStatus.textContent = error.message || 'Translation failed';
            } finally {
                translateAll.disabled = false;
            }
        });
        translatePanel.append(translateAll, translateStatus);
        blockEditor.append(translatePanel);

        const formGrid = createEl('div', 'cms-block-fields');
        renderLocaleFields(formGrid, block, 'heading', 'heading');
        renderLocaleFields(formGrid, block, 'body', 'body', { wide: true, multiline: true, rows: 6 });
        renderLocaleFields(formGrid, block, 'footer', 'footer', { wide: true, multiline: true, rows: 3 });
        renderItemsEditor(formGrid, block);
        renderFaqEditor(formGrid, block);
        renderContactEditor(formGrid, block);
        renderImagesEditor(formGrid, block);
        renderActionsEditor(formGrid, block);
        renderProductsEditor(formGrid, block);
        blockEditor.append(formGrid);
        renderBlockPreview(block);
    };

    const renderBlocks = () => {
        const blocks = parseBlocks();

        if (count) {
            count.textContent = `${blocks.length} ${blocks.length === 1 ? 'block' : 'blocks'}`;
        }

        if (blockList) {
            blockList.innerHTML = blocks.length ? '' : '<p class="hint">No blocks yet. Add one from the block library.</p>';
            blocks.forEach((block, index) => {
                const image = firstImage(block);
                const item = createEl('div', 'cms-block-item');
                const card = document.createElement('button');
                card.type = 'button';
                card.className = 'cms-block-card';
                card.classList.toggle('active', index === selectedBlock);
                card.innerHTML = `
                    ${image ? `<img class="cms-block-thumb" src="${image}" alt="">` : '<span class="cms-block-thumb"></span>'}
                    <div>
                        <h4>${blockTitle(block, index)}</h4>
                        <p>${blockSummary(block)}</p>
                    </div>
                    <span>${block.type || 'block'}</span>
                `;
                card.addEventListener('click', () => {
                    selectedBlock = index;
                    renderBlocks();
                    openBlockModal();
                });
                item.append(card);

                const controls = createEl('div', 'cms-block-controls');
                [
                    ['Up', () => {
                        const current = parseBlocks();
                        if (index === 0) return;
                        [current[index - 1], current[index]] = [current[index], current[index - 1]];
                        syncAndRender(current, index - 1);
                    }],
                    ['Down', () => {
                        const current = parseBlocks();
                        if (index >= current.length - 1) return;
                        [current[index + 1], current[index]] = [current[index], current[index + 1]];
                        syncAndRender(current, index + 1);
                    }],
                    ['Copy', () => {
                        const current = parseBlocks();
                        current.splice(index + 1, 0, structuredClone(current[index]));
                        syncAndRender(current, index + 1);
                    }],
                    ['Delete', () => {
                        const current = parseBlocks();
                        current.splice(index, 1);
                        syncAndRender(current, Math.max(index - 1, 0));
                    }],
                ].forEach(([label, handler]) => {
                    const button = createEl('button', `cms-icon-button ${label === 'Delete' ? 'danger' : ''}`, label);
                    button.type = 'button';
                    button.addEventListener('click', (event) => {
                        event.stopPropagation();
                        handler();
                    });
                    controls.append(button);
                });
                item.append(controls);
                blockList.append(item);
            });
        }

        if (outline) {
            outline.innerHTML = blocks.length ? '' : '<p class="hint">The page structure will appear here.</p>';
            blocks.forEach((block, index) => {
                const item = document.createElement('a');
                item.href = '#';
                item.textContent = `${index + 1}. ${blockTitle(block, index)}`;
                item.addEventListener('click', (event) => {
                    event.preventDefault();
                    activateTab('blocks');
                    selectedBlock = index;
                    renderBlocks();
                    openBlockModal();
                });
                outline.append(item);
            });
        }

        renderSelectedBlockEditor(blocks);
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => activateTab(tab.dataset.cmsTab));
    });

    if (previewLocaleSelect) {
        Object.entries(locales).forEach(([locale, localeLabel]) => {
            const option = document.createElement('option');
            option.value = locale;
            option.textContent = localeLabel;
            option.selected = locale === previewLocale;
            previewLocaleSelect.append(option);
        });
        previewLocaleSelect.addEventListener('change', () => {
            previewLocale = previewLocaleSelect.value;
            renderBlockPreview(parseBlocks()[selectedBlock]);
        });
    }

    editor.querySelectorAll('[data-cms-add-block]').forEach((button) => {
        button.addEventListener('click', () => {
            const blocks = parseBlocks();
            const template = blockTemplates[button.dataset.cmsAddBlock];

            if (!template || !jsonField) {
                return;
            }

            blocks.push(structuredClone(template));
            syncAndRender(blocks, blocks.length - 1);
            activateTab('blocks');
            openBlockModal();
        });
    });

    editor.querySelectorAll('[data-cms-close-block-modal]').forEach((button) => {
        button.addEventListener('click', closeBlockModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && blockModal?.classList.contains('is-open')) {
            closeBlockModal();
        }
    });

    editor.querySelector('[data-cms-format-json]')?.addEventListener('click', () => {
        if (!jsonField) {
            return;
        }

        const blocks = parseBlocks();
        syncAndRender(blocks);
    });

    jsonField?.addEventListener('input', renderBlocks);
    editor.addEventListener('submit', () => {
        const blocks = parseBlocks();
        writeBlocks(blocks);
    });
    renderBlocks();
});
