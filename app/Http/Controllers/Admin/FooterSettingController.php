<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.footer-settings.edit', [
            'locales' => Language::activeOptions(),
            'settings' => $this->settings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $localeCodes = array_keys(Language::activeOptions());

        $validated = $request->validate([
            'footer_description' => ['required', 'array'],
            'footer_description.*' => ['nullable', 'string', 'max:500'],
            'footer_email' => ['nullable', 'email', 'max:255'],
            'footer_phone' => ['nullable', 'string', 'max:60'],
        ]);

        $descriptions = [];

        foreach ($localeCodes as $locale) {
            $descriptions[$locale] = trim((string) data_get($validated, "footer_description.$locale", ''));
        }

        SiteSetting::setValue('footer_description', json_encode($descriptions));
        SiteSetting::setValue('footer_email', trim((string) ($validated['footer_email'] ?? '')));
        SiteSetting::setValue('footer_phone', trim((string) ($validated['footer_phone'] ?? '')));

        return redirect()->route('admin.footer-settings.edit')->with('status', 'Footer settings saved.');
    }

    private function settings(): array
    {
        $descriptions = json_decode(SiteSetting::getValue('footer_description', '{}') ?: '{}', true);

        if (! is_array($descriptions)) {
            $descriptions = [];
        }

        return [
            'footer_description' => $descriptions,
            'footer_email' => SiteSetting::getValue('footer_email', 'info@holasantana.com'),
            'footer_phone' => SiteSetting::getValue('footer_phone', '+34 624 229 511'),
        ];
    }
}
