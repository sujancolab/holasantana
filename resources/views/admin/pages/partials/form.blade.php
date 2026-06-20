@php
    $previewUrl = $page->exists
        ? route('pages.show', ['locale' => 'en', 'slug' => $page->slug])
        : null;
@endphp

<form method="post" action="{{ $action }}" class="cms-form cms-editor" data-cms-editor data-cms-locales='@json($locales)' data-upload-url="{{ route('admin.media.upload-image') }}">
    @csrf
    @if ($method === 'put') @method('put') @endif

    @if ($errors->any())
        <div class="error">Please check the highlighted fields.</div>
    @endif

    <div class="cms-editor-toolbar">
        <div>
            <p class="eyebrow">CMS editor</p>
            <h2>{{ $page->exists ? data_get($page->title, 'en', 'Untitled page') : 'Create new page' }}</h2>
            <span>{{ $previewUrl ?? 'Draft page' }}</span>
        </div>
        <div class="cms-editor-actions">
            <a class="button ghost" href="{{ route('admin.languages.index') }}">Manage languages</a>
            @if ($previewUrl)
                <a class="button ghost" href="{{ $previewUrl }}" target="_blank">Open page</a>
            @endif
            <button class="button" type="submit">Update</button>
        </div>
    </div>

    <div class="cms-workbench">
        <section class="cms-canvas">
            <div class="cms-tabs" role="tablist">
                <button type="button" class="active" data-cms-tab="content">Content</button>
                <button type="button" data-cms-tab="seo">SEO</button>
                <button type="button" data-cms-tab="blocks">Blocks</button>
                <button type="button" data-cms-tab="preview">Preview</button>
            </div>

            <section class="cms-tab-panel active" data-cms-panel="content" data-page-language-panels>
                @foreach ($locales as $locale => $label)
                    <div class="cms-card" data-page-locale-card="{{ $locale }}">
                        <div class="cms-card-head">
                            <h3>{{ $label }} content</h3>
                            <span>{{ strtoupper($locale) }}</span>
                        </div>
                        <div class="form-grid">
                            <label>Page title<input name="title[{{ $locale }}]" value="{{ old("title.$locale", data_get($page->title, $locale)) }}" @required($locale === 'en')></label>
                            <label>Menu label<input name="menu_label[{{ $locale }}]" value="{{ old("menu_label.$locale", data_get($page->menu_label, $locale)) }}"></label>
                            <label>Hero eyebrow<input name="hero_eyebrow[{{ $locale }}]" value="{{ old("hero_eyebrow.$locale", data_get($page->hero_eyebrow, $locale)) }}"></label>
                            <label>Hero title<input name="hero_title[{{ $locale }}]" value="{{ old("hero_title.$locale", data_get($page->hero_title, $locale)) }}" @required($locale === 'en')></label>
                            <label class="wide">Hero subtitle<textarea name="hero_subtitle[{{ $locale }}]" rows="3">{{ old("hero_subtitle.$locale", data_get($page->hero_subtitle, $locale)) }}</textarea></label>
                        </div>
                    </div>
                @endforeach
            </section>

            <section class="cms-tab-panel" data-cms-panel="seo" data-seo-language-panels>
                <div class="cms-card">
                    <div class="cms-card-head">
                        <h3>Search preview</h3>
                        <span>Meta</span>
                    </div>
                    @foreach ($locales as $locale => $label)
                        <label>{{ $label }} meta description<textarea name="meta_description[{{ $locale }}]" rows="4">{{ old("meta_description.$locale", data_get($page->meta_description, $locale)) }}</textarea></label>
                    @endforeach
                </div>
            </section>

            <section class="cms-tab-panel" data-cms-panel="blocks">
                <div class="cms-block-builder">
                    <aside class="cms-block-sidebar">
                        <button type="button" data-cms-add-block="text_section">Add text block</button>
                        <button type="button" data-cms-add-block="gallery">Add gallery</button>
                        <button type="button" data-cms-add-block="service_section">Add service</button>
                        <button type="button" data-cms-add-block="category_products">Add products</button>
                    </aside>
                    <div class="cms-block-main">
                        <div class="cms-card">
                            <div class="cms-card-head">
                                <h3>Page blocks</h3>
                                <button type="button" class="button ghost" data-cms-format-json>Repair JSON</button>
                            </div>
                            <div class="cms-dynamic-builder">
                                <div class="cms-block-list" data-cms-block-list></div>
                                <div class="cms-block-editor-panel" data-cms-block-editor></div>
                            </div>
                            <details class="cms-json-editor">
                                <summary>Advanced JSON</summary>
                                <label class="wide">Content blocks JSON
                                    <textarea name="content_blocks_json" rows="18" data-cms-json>{{ old('content_blocks_json', json_encode($page->content_blocks ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) }}</textarea>
                                </label>
                            </details>
                            <p class="hint">Use the visual fields for normal edits. Open Advanced JSON only when you need a custom block field that is not exposed yet.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="cms-tab-panel" data-cms-panel="preview">
                <div class="cms-preview-frame">
                    @if ($previewUrl)
                        <iframe src="{{ $previewUrl }}" title="Page preview"></iframe>
                    @else
                        <div class="cms-empty-preview">Save this page once to enable a live preview.</div>
                    @endif
                </div>
            </section>
        </section>

        <aside class="cms-inspector">
            <div class="cms-card">
                <div class="cms-card-head">
                    <h3>Publish</h3>
                    <span>{{ $page->exists ? 'Saved' : 'New' }}</span>
                </div>
                <label>Status
                    <select name="status">
                        <option value="published" @selected(old('status', $page->status) === 'published')>Published</option>
                        <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option>
                    </select>
                </label>
                <label class="checkbox"><input name="show_in_menu" type="checkbox" value="1" @checked(old('show_in_menu', $page->show_in_menu))> Show in menu</label>
                <button class="button full" type="submit">Save page</button>
            </div>

            <div class="cms-card">
                <div class="cms-card-head">
                    <h3>Page settings</h3>
                    <span>URL</span>
                </div>
                <label>Slug<input name="slug" value="{{ old('slug', $page->slug) }}" required></label>
                <label>Template
                    <select name="template">
                        @foreach (['default' => 'Default', 'home' => 'Home', 'prime' => 'Prime site', 'contact' => 'Contact', 'blog' => 'Blog'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('template', $page->template) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Menu order<input name="menu_order" type="number" min="0" value="{{ old('menu_order', $page->menu_order ?? 0) }}"></label>
            </div>

            <div class="cms-card">
                <div class="cms-card-head">
                    <h3>Structure</h3>
                    <span data-cms-block-count>0 blocks</span>
                </div>
                <div class="cms-outline" data-cms-outline></div>
            </div>
        </aside>
    </div>

    <div class="form-actions">
        <button class="button" type="submit">Save page</button>
        <a class="button ghost" href="{{ route('admin.pages.index') }}">Cancel</a>
    </div>
</form>

@if ($page->exists)
    <form method="post" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">
        @csrf
        @method('delete')
        <button class="button danger" type="submit">Delete page</button>
    </form>
@endif
