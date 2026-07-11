@extends('layouts.admin')

@section('title', 'Languages')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>AI translation</h2>
                <p class="hint">Used by the CMS block editor to translate from English into each enabled language.</p>
            </div>
        </div>
        <form method="post" action="{{ route('admin.translations.settings') }}" class="cms-form">
            @csrf
            <div class="form-grid">
                <label>Gemini API key
                    <input name="gemini_api_key" type="password" placeholder="{{ $translationSettings['has_api_key'] ? 'Saved - enter a new key to replace' : 'Paste your Gemini API key' }}">
                </label>
                <label>Gemini model
                    <input name="gemini_model" list="gemini-models" value="{{ old('gemini_model', $translationSettings['model']) }}" placeholder="gemini-3.5-flash" required>
                    <datalist id="gemini-models">
                        <option value="gemini-3.5-flash">Gemini 3.5 Flash</option>
                        <option value="gemini-flash-latest">Gemini Flash latest</option>
                        <option value="gemini-3.1-flash-lite">Gemini 3.1 Flash Lite</option>
                        <option value="gemini-2.5-flash-lite">Gemini 2.5 Flash Lite</option>
                    </datalist>
                    <span class="hint">Use a Flash or Flash Lite model for the free/low-cost Gemini API path.</span>
                </label>
            </div>
            <div class="form-actions">
                <button class="button" type="submit">Save translation settings</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Language module</h2>
        </div>
        <form method="post" action="{{ route('admin.languages.store') }}" class="language-create-form">
            @csrf
            <label>Code<input name="code" placeholder="fr" required></label>
            <label>Name<input name="name" placeholder="French" required></label>
            <label>Order<input name="sort_order" type="number" min="0" value="{{ $language->sort_order }}"></label>
            <label class="checkbox"><input name="is_active" type="checkbox" value="1" checked> Active</label>
            <label class="checkbox"><input name="is_default" type="checkbox" value="1"> Default</label>
            <button class="button" type="submit">Add language</button>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Enabled languages</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Order</th><th>Language</th><th>Code</th><th>Status</th><th>Default</th><th></th></tr></thead>
                <tbody>
                @foreach ($languages as $language)
                    <tr>
                        <td>{{ $language->sort_order }}</td>
                        <td>{{ $language->name }}</td>
                        <td>{{ $language->code }}</td>
                        <td>{{ $language->is_active ? 'Active' : 'Disabled' }}</td>
                        <td>{{ $language->is_default ? 'Yes' : 'No' }}</td>
                        <td><a href="{{ route('admin.languages.edit', $language) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
