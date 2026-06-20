<form method="post" action="{{ $action }}" class="cms-form">
    @csrf
    @if ($method === 'put') @method('put') @endif

    @if ($errors->any())
        <div class="error">Please check the highlighted fields.</div>
    @endif

    <div class="form-grid">
        <label>Slug<input name="slug" value="{{ old('slug', $page->slug) }}" required></label>
        <label>Template
            <select name="template">
                @foreach (['default' => 'Default', 'home' => 'Home', 'contact' => 'Contact', 'blog' => 'Blog'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('template', $page->template) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>Status
            <select name="status">
                <option value="published" @selected(old('status', $page->status) === 'published')>Published</option>
                <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft</option>
            </select>
        </label>
        <label>Menu order<input name="menu_order" type="number" min="0" value="{{ old('menu_order', $page->menu_order ?? 0) }}"></label>
        <label class="checkbox"><input name="show_in_menu" type="checkbox" value="1" @checked(old('show_in_menu', $page->show_in_menu))> Show in menu</label>
    </div>

    @foreach ($locales as $locale => $label)
        <div class="language-panel">
            <h2>{{ $label }} content</h2>
            <div class="form-grid">
                <label>Page title<input name="title[{{ $locale }}]" value="{{ old("title.$locale", data_get($page->title, $locale)) }}" @required($locale === 'en')></label>
                <label>Menu label<input name="menu_label[{{ $locale }}]" value="{{ old("menu_label.$locale", data_get($page->menu_label, $locale)) }}"></label>
                <label class="wide">Meta description<textarea name="meta_description[{{ $locale }}]" rows="2">{{ old("meta_description.$locale", data_get($page->meta_description, $locale)) }}</textarea></label>
                <label>Hero eyebrow<input name="hero_eyebrow[{{ $locale }}]" value="{{ old("hero_eyebrow.$locale", data_get($page->hero_eyebrow, $locale)) }}"></label>
                <label>Hero title<input name="hero_title[{{ $locale }}]" value="{{ old("hero_title.$locale", data_get($page->hero_title, $locale)) }}" @required($locale === 'en')></label>
                <label class="wide">Hero subtitle<textarea name="hero_subtitle[{{ $locale }}]" rows="3">{{ old("hero_subtitle.$locale", data_get($page->hero_subtitle, $locale)) }}</textarea></label>
            </div>
        </div>
    @endforeach

    <div class="language-panel">
        <h2>Content sections</h2>
        <label class="wide">Advanced content blocks JSON
            <textarea name="content_blocks_json" rows="12">{{ old('content_blocks_json', json_encode($page->content_blocks ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) }}</textarea>
        </label>
        <p class="hint">Use this field for image galleries, custom home sections, and larger page structures. The simpler fields below are best for basic pages.</p>
        @for ($i = 0; $i < 4; $i++)
            @php($block = old("blocks.$i", $blocks[$i] ?? []))
            <div class="block-editor">
                <h3>Section {{ $i + 1 }}</h3>
                @foreach ($locales as $locale => $label)
                    <label>{{ $label }} heading<input name="blocks[{{ $i }}][heading][{{ $locale }}]" value="{{ data_get($block, "heading.$locale") }}"></label>
                    <label>{{ $label }} body<textarea name="blocks[{{ $i }}][body][{{ $locale }}]" rows="4">{{ data_get($block, "body.$locale") }}</textarea></label>
                    <label>{{ $label }} button text<input name="blocks[{{ $i }}][button_text][{{ $locale }}]" value="{{ data_get($block, "button_text.$locale") }}"></label>
                @endforeach
                <label>Button URL<input name="blocks[{{ $i }}][button_url]" value="{{ data_get($block, 'button_url') }}"></label>
            </div>
        @endfor
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
