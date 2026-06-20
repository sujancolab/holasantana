<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    private array $locales = ['en' => 'English', 'es' => 'Spanish'];

    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::orderBy('menu_order')->orderBy('title->en')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create', [
            'page' => new Page([
                'status' => 'published',
                'show_in_menu' => true,
                'menu_order' => Page::max('menu_order') + 1,
            ]),
            'locales' => $this->locales,
            'blocks' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $page = Page::create($this->payload($request));
        $this->syncMenuItem($page);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page created.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
            'locales' => $this->locales,
            'blocks' => $page->content_blocks ?? [],
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->payload($request, $page));
        $this->syncMenuItem($page);

        return redirect()->route('admin.pages.edit', $page)->with('status', 'Page updated.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Page deleted.');
    }

    private function payload(Request $request, ?Page $page = null): array
    {
        $validated = $request->validate([
            'slug' => ['required', 'regex:/^[a-z0-9\/-]+$/', Rule::unique('pages', 'slug')->ignore($page)],
            'template' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'menu_order' => ['required', 'integer', 'min:0'],
            'show_in_menu' => ['nullable', 'boolean'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.es' => ['nullable', 'string', 'max:255'],
            'menu_label.en' => ['nullable', 'string', 'max:255'],
            'menu_label.es' => ['nullable', 'string', 'max:255'],
            'meta_description.en' => ['nullable', 'string', 'max:500'],
            'meta_description.es' => ['nullable', 'string', 'max:500'],
            'hero_eyebrow.en' => ['nullable', 'string', 'max:255'],
            'hero_eyebrow.es' => ['nullable', 'string', 'max:255'],
            'hero_title.en' => ['required', 'string', 'max:255'],
            'hero_title.es' => ['nullable', 'string', 'max:255'],
            'hero_subtitle.en' => ['nullable', 'string'],
            'hero_subtitle.es' => ['nullable', 'string'],
            'blocks' => ['nullable', 'array'],
            'blocks.*.heading.en' => ['nullable', 'string', 'max:255'],
            'blocks.*.heading.es' => ['nullable', 'string', 'max:255'],
            'blocks.*.body.en' => ['nullable', 'string'],
            'blocks.*.body.es' => ['nullable', 'string'],
            'blocks.*.button_text.en' => ['nullable', 'string', 'max:100'],
            'blocks.*.button_text.es' => ['nullable', 'string', 'max:100'],
            'blocks.*.button_url' => ['nullable', 'string', 'max:255'],
            'content_blocks_json' => ['nullable', 'json'],
        ]);

        $contentBlocksJson = trim((string) ($validated['content_blocks_json'] ?? ''));

        if (filled($contentBlocksJson) && $contentBlocksJson !== '[]') {
            $blocks = json_decode($validated['content_blocks_json'], true, flags: JSON_THROW_ON_ERROR);
        } else {
            $blocks = collect($validated['blocks'] ?? [])
                ->filter(fn (array $block) => filled(data_get($block, 'heading.en')) || filled(data_get($block, 'body.en')))
                ->values()
                ->all();
        }

        return [
            'slug' => trim(strtolower($validated['slug']), '/'),
            'template' => $validated['template'],
            'status' => $validated['status'],
            'menu_order' => $validated['menu_order'],
            'show_in_menu' => $request->boolean('show_in_menu'),
            'title' => $validated['title'],
            'menu_label' => $validated['menu_label'] ?? $validated['title'],
            'meta_description' => $validated['meta_description'] ?? [],
            'hero_eyebrow' => $validated['hero_eyebrow'] ?? [],
            'hero_title' => $validated['hero_title'],
            'hero_subtitle' => $validated['hero_subtitle'] ?? [],
            'content_blocks' => $blocks,
        ];
    }

    private function syncMenuItem(Page $page): void
    {
        if (! $page->show_in_menu) {
            $page->menuItem()->delete();
            return;
        }

        MenuItem::updateOrCreate(
            ['page_id' => $page->id],
            [
                'label' => $page->menu_label ?: $page->title,
                'sort_order' => $page->menu_order,
                'is_active' => $page->status === 'published',
            ],
        );
    }
}
