<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Language;
use App\Models\HolidayHome;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
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

    public function faq(Request $request): RedirectResponse
    {
        return redirect()->route('pages.show', [
            'locale' => $this->preferredLocale($request),
            'slug' => 'faq',
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
            'holidayHomes' => $page->slug === 'home-rental'
                ? HolidayHome::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
                : collect(),
            'menuItems' => MenuItem::with('page')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function storeServiceEnquiry(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('serviceEnquiry', [
            'service_name' => ['required', 'string', 'max:255'],
            'enquiry_date' => ['nullable', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['required', 'string', 'max:50'],
        ]);

        $message = implode(PHP_EOL, [
            'New service enquiry from Hola Santana',
            '',
            'Service Name: ' . $data['service_name'],
            'Enquiry Date: ' . ($data['enquiry_date'] ?? now()->toDateString()),
            'Name: ' . $data['name'],
            'Email Address: ' . $data['email'],
            'Telephone Number: ' . $data['telephone'],
        ]);

        Mail::raw($message, function ($mail) use ($data) {
            $mail->to('spm3182@gmail.com')
                ->replyTo($data['email'], $data['name'])
                ->subject('Hola Santana Service Enquiry: ' . $data['service_name']);
        });

        return back()->with('service_enquiry_status', 'Your service enquiry has been sent.');
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
