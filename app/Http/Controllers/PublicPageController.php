<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return $this->show(Language::defaultCode(), 'home');
    }

    public function show(string $locale, string $slug = 'home'): View
    {
        abort_unless((bool) preg_match('/^[a-z]{2,3}(?:-[a-z]{2})?$/i', $locale), 404);
        abort_unless(array_key_exists($locale, Language::activeOptions()), 404);

        App::setLocale($locale);

        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('public.page', [
            'page' => $page,
            'locale' => $locale,
            'availableLocales' => $this->pageLocales($page),
            'menuItems' => MenuItem::with('page')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    private function pageLocales(Page $page): array
    {
        $codes = array_keys(Language::activeOptions());

        foreach (['title', 'menu_label', 'meta_description', 'hero_eyebrow', 'hero_title', 'hero_subtitle'] as $field) {
            $value = $page->{$field};

            if (is_array($value)) {
                $codes = array_merge($codes, array_keys($value));
            }
        }

        $this->collectLocaleCodes($page->content_blocks ?? [], $codes);

        return array_values(array_unique(array_filter($codes, fn (string $code) => preg_match('/^[a-z]{2,3}(?:-[a-z]{2})?$/i', $code))));
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
