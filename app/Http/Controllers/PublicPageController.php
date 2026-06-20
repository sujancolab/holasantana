<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(Request $request): RedirectResponse
    {
        return redirect()->route('pages.show', [
            'locale' => $this->preferredLocale($request),
            'slug' => 'home',
        ]);
    }

    public function show(Request $request, string $locale, string $slug = 'home'): View
    {
        abort_unless((bool) preg_match('/^[a-z]{2,3}(?:-[a-z]{2})?$/i', $locale), 404);
        abort_unless(array_key_exists($locale, Language::activeOptions()), 404);

        App::setLocale($locale);
        $request->session()->put('site_locale', $locale);
        Cookie::queue(cookie('site_locale', $locale, 60 * 24 * 365, null, null, false, false, false, 'lax'));

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
        return array_keys(Language::activeOptions());
    }

    private function preferredLocale(Request $request): string
    {
        $activeLocales = Language::activeOptions();

        foreach ([$request->session()->get('site_locale'), $request->cookie('site_locale')] as $locale) {
            if (is_string($locale) && array_key_exists($locale, $activeLocales)) {
                return $locale;
            }
        }

        return Language::defaultCode();
    }
}
