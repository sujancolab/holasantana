@extends('layouts.admin')

@section('title', 'Languages')

@section('content')
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
