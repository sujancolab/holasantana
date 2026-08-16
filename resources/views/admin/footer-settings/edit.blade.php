@extends('layouts.admin')

@section('title', 'Footer Settings')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Public footer</h2>
                <p class="hint">Translate the footer description and update the shared contact details shown at the bottom of the website.</p>
            </div>
        </div>

        <form method="post" action="{{ route('admin.footer-settings.update') }}" class="cms-form">
            @csrf
            @method('put')

            <div class="language-panel">
                <div class="cms-card-head">
                    <h3>Footer description</h3>
                    <span>Translated</span>
                </div>

                <div class="form-grid">
                    @foreach ($locales as $locale => $label)
                        <label class="wide">{{ $label }} description
                            <textarea name="footer_description[{{ $locale }}]" rows="3">{{ old("footer_description.$locale", data_get($settings, "footer_description.$locale", $locale === 'en' ? 'Home care and holiday rental management in Torrevieja.' : '')) }}</textarea>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-grid">
                <label>Email
                    <input name="footer_email" type="email" value="{{ old('footer_email', data_get($settings, 'footer_email')) }}" placeholder="info@holasantana.com">
                </label>
                <label>Phone number
                    <input name="footer_phone" value="{{ old('footer_phone', data_get($settings, 'footer_phone')) }}" placeholder="+34 624 229 511">
                </label>
            </div>

            <div class="form-actions">
                <button class="button" type="submit">Save footer</button>
            </div>
        </form>
    </section>
@endsection
