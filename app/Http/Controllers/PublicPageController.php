<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Language;
use App\Models\HolidayHome;
use App\Models\SiteSetting;
use App\Support\WixAssetUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    private const MAIL_RECIPIENTS = [
        'spm3182@gmail.com',
        'Info@santanaprime.es',
    ];

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
        $page->content_blocks = WixAssetUrl::localize($page->content_blocks ?? []);

        $holidayHomes = $page->slug === 'home-rental'
            ? HolidayHome::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
            : collect();

        $holidayHomes->each(function (HolidayHome $holidayHome) {
            $holidayHome->image_url = WixAssetUrl::localize($holidayHome->image_url);
        });

        return view('public.page', [
            'page' => $page,
            'locale' => $locale,
            'availableLocales' => $this->pageLocales($page),
            'holidayHomes' => $holidayHomes,
            'footerSettings' => $this->footerSettings($locale),
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
            $mail->to(self::MAIL_RECIPIENTS)
                ->replyTo($data['email'], $data['name'])
                ->subject('Hola Santana Service Enquiry: ' . $data['service_name']);
        });

        return back()->with('service_enquiry_status', 'Your service enquiry has been sent.');
    }

    public function storeSubmitQuery(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('submitQuery', [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'property_address' => ['nullable', 'string', 'max:255'],
            'ordering_date' => ['nullable', 'date'],
            'service_area' => ['nullable', 'string', 'max:255'],
            'service_date' => ['nullable', 'date'],
            'service_time' => ['nullable', 'date_format:H:i'],
            'contact_method' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = implode(PHP_EOL, [
            'New submit query from Hola Santana',
            '',
            'First Name: ' . $data['first_name'],
            'Last Name: ' . $data['last_name'],
            'Telephone Number: ' . $data['telephone'],
            'Email Address: ' . ($data['email'] ?? ''),
            'Property Address: ' . ($data['property_address'] ?? ''),
            'Ordering Date: ' . ($data['ordering_date'] ?? ''),
            'Service Area: ' . ($data['service_area'] ?? ''),
            'Service Date: ' . ($data['service_date'] ?? ''),
            'Approximate Service Time: ' . ($data['service_time'] ?? ''),
            'Preferred Contact Method: ' . ($data['contact_method'] ?? ''),
            '',
            'Message:',
            $data['message'],
        ]);

        Mail::raw($message, function ($mail) use ($data) {
            $mail->to(self::MAIL_RECIPIENTS)
                ->subject('Hola Santana Submit Query: ' . trim($data['first_name'] . ' ' . $data['last_name']));

            if (filled($data['email'] ?? null)) {
                $mail->replyTo($data['email'], trim($data['first_name'] . ' ' . $data['last_name']));
            }
        });

        return back()->with('submit_query_status', 'Your query has been sent.');
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

    private function footerSettings(string $locale): array
    {
        $descriptions = json_decode(SiteSetting::getValue('footer_description', '{}') ?: '{}', true);

        if (! is_array($descriptions)) {
            $descriptions = [];
        }

        $description = $descriptions[$locale]
            ?? $descriptions['en']
            ?? 'Home care and holiday rental management in Torrevieja.';
        $email = SiteSetting::getValue('footer_email', 'info@holasantana.com') ?: 'info@holasantana.com';
        $phone = SiteSetting::getValue('footer_phone', '+34 624 229 511') ?: '+34 624 229 511';
        $phoneHref = preg_replace('/[^\d+]/', '', $phone) ?: $phone;

        return [
            'description' => $description,
            'email' => $email,
            'phone' => $phone,
            'phone_href' => $phoneHref,
        ];
    }
}
