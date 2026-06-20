@extends('layouts.admin')

@section('title', 'Page Management')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <h2>Pages and menus</h2>
            <a class="button" href="{{ route('admin.pages.create') }}">Add page</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Order</th><th>Page</th><th>URL</th><th>Menu</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($pages as $page)
                    <tr>
                        <td>{{ $page->menu_order }}</td>
                        <td>{{ $page->localized('title', 'en') }}</td>
                        <td><a href="{{ route('pages.show', ['locale' => 'en', 'slug' => $page->slug]) }}" target="_blank">/{{ $page->slug }}</a></td>
                        <td>{{ $page->show_in_menu ? 'Shown' : 'Hidden' }}</td>
                        <td>{{ ucfirst($page->status) }}</td>
                        <td><a href="{{ route('admin.pages.edit', $page) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $pages->links() }}
    </section>
@endsection
