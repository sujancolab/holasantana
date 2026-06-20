@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <section class="stat-grid">
        <div><strong>{{ $pageCount }}</strong><span>Total pages</span></div>
        <div><strong>{{ $publishedCount }}</strong><span>Published</span></div>
        <div><strong>{{ $menuCount }}</strong><span>Menu items</span></div>
    </section>
    <section class="panel">
        <div class="panel-head">
            <h2>Recent pages</h2>
            <a class="button" href="{{ route('admin.pages.create') }}">Add page</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Page</th><th>Slug</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($recentPages as $page)
                    <tr>
                        <td>{{ $page->localized('title', 'en') }}</td>
                        <td>{{ $page->slug }}</td>
                        <td>{{ ucfirst($page->status) }}</td>
                        <td><a href="{{ route('admin.pages.edit', $page) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
