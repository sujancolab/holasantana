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

    menu.querySelectorAll('a').forEach((link) => {
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
            const link = createEl('a', `prime-button ${action.class || ''}`, label);
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
                const button = createEl('button', '', 'Order It');
                button.type = 'button';
                card.append(media, createEl('h2', '', product.name || 'Product'), button);
                grid.append(card);
            });
            section.append(grid);
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
        const action = Array.isArray(block.actions) ? block.actions[0] || {} : {};
        const card = createEl('div', 'cms-subeditor');
        const head = createEl('div', 'cms-subeditor-head');
        head.append(createEl('h4', '', 'Button'));
        card.append(head);

        Object.entries(locales).forEach(([locale, localeLabel]) => {
            card.append(field(`${localeLabel} label`, getLocalized(action.label, locale), (value) => {
                const blocks = parseBlocks();
                blocks[selectedBlock].actions ??= [{}];
                blocks[selectedBlock].actions[0].label ??= {};
                blocks[selectedBlock].actions[0].label[locale] = value;
                writeBlocks(blocks);
                renderBlockPreview(blocks[selectedBlock]);
            }));
        });

        card.append(field('URL', action.url || '', (value) => {
            const blocks = parseBlocks();
            blocks[selectedBlock].actions ??= [{}];
            blocks[selectedBlock].actions[0].url = value;
            writeBlocks(blocks);
            renderBlockPreview(blocks[selectedBlock]);
        }, { placeholder: '/en/contact' }));
        container.append(card);
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
            };
            const hasFields = Object.values(fields).some(Boolean);
            const targetLocales = Object.keys(locales).filter((locale) => locale !== 'en');

            if (!hasFields) {
                translateStatus.textContent = 'Add English heading, body, or footer first';
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
