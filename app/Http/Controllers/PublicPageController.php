<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return $this->show('en', 'home');
    }

    public function show(string $locale, string $slug = 'home'): View
    {
        abort_unless(in_array($locale, ['en', 'es'], true), 404);

        App::setLocale($locale);

        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('public.page', [
            'page' => $page,
            'locale' => $locale,
            'menuItems' => MenuItem::with('page')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
