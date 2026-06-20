@extends('layouts.admin')

@section('title', 'Edit Language')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <h2>{{ $language->name }}</h2>
            <a class="button ghost" href="{{ route('admin.languages.index') }}">Back</a>
        </div>
        <form method="post" action="{{ route('admin.languages.update', $language) }}" class="cms-form">
            @csrf
            @method('put')
            <div class="form-grid">
                <label>Code<input name="code" value="{{ old('code', $language->code) }}" required></label>
                <label>Name<input name="name" value="{{ old('name', $language->name) }}" required></label>
                <label>Order<input name="sort_order" type="number" min="0" value="{{ old('sort_order', $language->sort_order) }}"></label>
                <label class="checkbox"><input name="is_active" type="checkbox" value="1" @checked(old('is_active', $language->is_active))> Active</label>
                <label class="checkbox"><input name="is_default" type="checkbox" value="1" @checked(old('is_default', $language->is_default))> Default language</label>
            </div>
            <div class="form-actions">
                <button class="button" type="submit">Save language</button>
            </div>
        </form>
    </section>

    @if (! $language->is_default)
        <form method="post" action="{{ route('admin.languages.destroy', $language) }}" onsubmit="return confirm('Delete this language?')">
            @csrf
            @method('delete')
            <button class="button danger" type="submit">Delete language</button>
        </form>
    @endif
@endsection
