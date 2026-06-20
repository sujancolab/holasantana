<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
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
            'locales' => Language::activeOptions(),
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
            'locales' => $this->pageLocales($page),
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

    public function uploadImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $validated['image']->store('cms-images', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    private function payload(Request $request, ?Page $page = null): array
    {
        $validated = $request->validate([
            'slug' => ['required', 'regex:/^[a-z0-9\/-]+$/', Rule::unique('pages', 'slug')->ignore($page)],
            'template' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'menu_order' => ['required', 'integer', 'min:0'],
            'show_in_menu' => ['nullable', 'boolean'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.*' => ['nullable', 'string', 'max:255'],
            'menu_label' => ['nullable', 'array'],
            'menu_label.*' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'array'],
            'meta_description.*' => ['nullable', 'string', 'max:500'],
            'hero_eyebrow' => ['nullable', 'array'],
            'hero_eyebrow.*' => ['nullable', 'string', 'max:255'],
            'hero_title' => ['required', 'array'],
            'hero_title.en' => ['required', 'string', 'max:255'],
            'hero_title.*' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'array'],
            'hero_subtitle.*' => ['nullable', 'string'],
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

    private function pageLocales(Page $page): array
    {
        $moduleLocales = Language::activeOptions();
        $codes = array_keys($moduleLocales);

        foreach (['title', 'menu_label', 'meta_description', 'hero_eyebrow', 'hero_title', 'hero_subtitle'] as $field) {
            $value = $page->{$field};

            if (is_array($value)) {
                $codes = array_merge($codes, array_keys($value));
            }
        }

        $this->collectLocaleCodes($page->content_blocks ?? [], $codes);
        $codes = array_values(array_unique(array_filter($codes, fn (string $code) => preg_match('/^[a-z]{2,3}(?:-[a-z]{2})?$/i', $code))));

        return collect($codes)
            ->mapWithKeys(fn (string $code) => [$code => $moduleLocales[$code] ?? strtoupper($code)])
            ->all();
    }

    private function collectLocaleCodes(mixed $value, array &$codes): void
    {
        if (! is_array($value)) {
            return;
        }

        foreach ($value as $key => $child) {
            if (is_string($key) && preg_match('/^[a-z]{2,3}(?:-[a-z]{2})?$/i', $key)) {
                $codes[] = $key;
            }

            $this->collectLocaleCodes($child, $codes);
        }
    }
}
