<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class TranslationController extends Controller
{
    private const DEFAULT_MODEL = 'gemini-3.5-flash';

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gemini_api_key' => ['nullable', 'string', 'max:500'],
            'gemini_model' => ['required', 'string', 'max:100'],
        ]);

        if ($request->filled('gemini_api_key')) {
            SiteSetting::setValue('gemini_api_key', Crypt::encryptString(trim($validated['gemini_api_key'])));
        }

        SiteSetting::setValue('gemini_model', trim($validated['gemini_model']));

        return redirect()->route('admin.languages.index')->with('status', 'Translation settings saved.');
    }

    public function translate(Request $request): JsonResponse
    {
        $languageCodes = array_keys(Language::activeOptions());

        $validated = $request->validate([
            'text' => ['required', 'string'],
            'source_locale' => ['required', Rule::in($languageCodes)],
            'target_locale' => ['required', Rule::in($languageCodes)],
            'field' => ['nullable', 'string', 'max:40'],
        ]);

        $encryptedApiKey = SiteSetting::getValue('gemini_api_key');

        if (! filled($encryptedApiKey)) {
            return response()->json([
                'message' => 'Add a Gemini API key in Languages before translating.',
            ], 422);
        }

        $apiKey = Crypt::decryptString($encryptedApiKey);

        $sourceName = Language::activeOptions()[$validated['source_locale']] ?? strtoupper($validated['source_locale']);
        $targetName = Language::activeOptions()[$validated['target_locale']] ?? strtoupper($validated['target_locale']);
        $model = SiteSetting::getValue('gemini_model', self::DEFAULT_MODEL) ?: self::DEFAULT_MODEL;
        $field = $validated['field'] ?? 'content';
        $prompt = <<<PROMPT
Translate this {$field} from {$sourceName} ({$validated['source_locale']}) to {$targetName} ({$validated['target_locale']}).
Return only the translated text. Preserve line breaks, punctuation, URLs, names, and any HTML exactly where appropriate.

{$validated['text']}
PROMPT;

        $lastError = 'Translation failed. Check your Gemini key and model.';

        foreach ($this->translationModels($model) as $candidateModel) {
            $response = Http::timeout(30)
                ->retry(1, 250)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$candidateModel}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                    ],
                ]);

            if (! $response->successful()) {
                $lastError = data_get($response->json(), 'error.message', $lastError);
                continue;
            }

            $translated = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text'));

            if ($translated === '') {
                $lastError = 'Gemini returned an empty translation.';
                continue;
            }

            if ($candidateModel !== $model) {
                SiteSetting::setValue('gemini_model', $candidateModel);
            }

            return response()->json([
                'translation' => $translated,
                'model' => $candidateModel,
            ]);
        }

        return response()->json([
            'message' => $this->friendlyModelError($lastError),
        ], 422);
    }

    public function translateBlock(Request $request): JsonResponse
    {
        $languages = Language::activeOptions();
        $languageCodes = array_keys($languages);

        $validated = $request->validate([
            'source_locale' => ['required', Rule::in($languageCodes)],
            'target_locales' => ['required', 'array', 'min:1'],
            'target_locales.*' => ['required', Rule::in($languageCodes)],
            'fields' => ['required', 'array'],
            'fields.heading' => ['nullable', 'string'],
            'fields.body' => ['nullable', 'string'],
            'fields.footer' => ['nullable', 'string'],
            'fields.items' => ['nullable', 'string'],
        ]);

        $fields = collect($validated['fields'])
            ->only(['heading', 'body', 'footer', 'items'])
            ->filter(fn (?string $value) => filled($value))
            ->all();

        if ($fields === []) {
            return response()->json([
                'message' => 'Add English heading, body, footer, or selection list text first.',
            ], 422);
        }

        $encryptedApiKey = SiteSetting::getValue('gemini_api_key');

        if (! filled($encryptedApiKey)) {
            return response()->json([
                'message' => 'Add a Gemini API key in Languages before translating.',
            ], 422);
        }

        $apiKey = Crypt::decryptString($encryptedApiKey);
        $model = SiteSetting::getValue('gemini_model', self::DEFAULT_MODEL) ?: self::DEFAULT_MODEL;
        $targets = collect($validated['target_locales'])
            ->unique()
            ->reject(fn (string $locale) => $locale === $validated['source_locale'])
            ->mapWithKeys(fn (string $locale) => [$locale => $languages[$locale] ?? strtoupper($locale)])
            ->all();

        if ($targets === []) {
            return response()->json([
                'message' => 'Choose at least one non-English language.',
            ], 422);
        }

        $sourceName = $languages[$validated['source_locale']] ?? strtoupper($validated['source_locale']);
        $targetJson = json_encode($targets, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $fieldJson = json_encode($fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $prompt = <<<PROMPT
Translate this CMS page block from {$sourceName} ({$validated['source_locale']}) to every target language.
Target languages JSON object, keyed by locale code:
{$targetJson}

English source fields JSON object:
{$fieldJson}

Return only valid JSON, with this exact shape:
{
  "translations": {
      "locale_code": {
        "heading": "translated heading if source heading exists",
        "body": "translated body if source body exists",
        "footer": "translated footer if source footer exists",
        "items": "translated selection list if source items exists, preserving one item per line"
      }
  }
}

Preserve line breaks, punctuation, URLs, brand names, places, and HTML exactly where appropriate. Do not include explanations or markdown.
PROMPT;

        $lastError = 'Translation failed. Check your Gemini key and model.';

        foreach ($this->translationModels($model) as $candidateModel) {
            $response = Http::timeout(45)
                ->retry(1, 250)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$candidateModel}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

            if (! $response->successful()) {
                $lastError = data_get($response->json(), 'error.message', $lastError);
                continue;
            }

            $rawText = trim((string) data_get($response->json(), 'candidates.0.content.parts.0.text'));
            $payload = $this->decodeJsonText($rawText);
            $translations = $payload['translations'] ?? null;

            if (! is_array($translations)) {
                $lastError = 'Gemini did not return usable translation JSON.';
                continue;
            }

            if ($candidateModel !== $model) {
                SiteSetting::setValue('gemini_model', $candidateModel);
            }

            return response()->json([
                'translations' => $translations,
                'model' => $candidateModel,
            ]);
        }

        return response()->json([
            'message' => $this->friendlyModelError($lastError),
        ], 422);
    }

    private function translationModels(string $preferredModel): array
    {
        return array_values(array_unique(array_filter([
            $preferredModel,
            self::DEFAULT_MODEL,
            'gemini-flash-latest',
            'gemini-3.1-flash-lite',
            'gemini-2.5-flash-lite',
        ])));
    }

    private function friendlyModelError(string $message): string
    {
        if (str_contains($message, 'not manageable to new users')) {
            return 'This Gemini model is not available for your key. I tried the free Flash fallbacks too; update the model in Languages or check your key in Google AI Studio.';
        }

        return $message;
    }

    private function decodeJsonText(string $text): array
    {
        $clean = preg_replace('/^```(?:json)?\s*|\s*```$/', '', trim($text));
        $decoded = json_decode($clean ?: '{}', true);

        return is_array($decoded) ? $decoded : [];
    }
}
