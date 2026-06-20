<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LanguageController extends Controller
{
    public function index(): View
    {
        return view('admin.languages.index', [
            'languages' => Language::orderByDesc('is_default')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'language' => new Language([
                'is_active' => true,
                'sort_order' => (int) Language::max('sort_order') + 1,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $language = Language::create($this->payload($request));
        $this->syncDefault($language, $request->boolean('is_default'));

        return redirect()->route('admin.languages.index')->with('status', 'Language added.');
    }

    public function edit(Language $language): View
    {
        return view('admin.languages.edit', [
            'language' => $language,
        ]);
    }

    public function update(Request $request, Language $language): RedirectResponse
    {
        $language->update($this->payload($request, $language));
        $this->syncDefault($language, $request->boolean('is_default'));

        return redirect()->route('admin.languages.index')->with('status', 'Language updated.');
    }

    public function destroy(Language $language): RedirectResponse
    {
        abort_if($language->is_default, 422, 'Default language cannot be deleted.');

        $language->delete();

        return redirect()->route('admin.languages.index')->with('status', 'Language deleted.');
    }

    private function payload(Request $request, ?Language $language = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'regex:/^[a-z]{2,3}(?:-[a-z]{2})?$/i',
                Rule::unique('languages', 'code')->ignore($language),
            ],
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        return [
            'code' => strtolower(str_replace('_', '-', $validated['code'])),
            'name' => $validated['name'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
            'is_default' => $request->boolean('is_default'),
        ];
    }

    private function syncDefault(Language $language, bool $isDefault): void
    {
        if (! $isDefault) {
            if (! Language::where('is_default', true)->exists()) {
                $language->forceFill(['is_default' => true, 'is_active' => true])->save();
            }

            return;
        }

        Language::whereKeyNot($language->id)->update(['is_default' => false]);
        $language->forceFill(['is_default' => true, 'is_active' => true])->save();
    }
}
