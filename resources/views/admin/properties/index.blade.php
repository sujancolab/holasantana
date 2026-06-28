@extends('layouts.admin')

@section('title', 'Properties')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <h2>Property Table</h2>
            <a class="button" href="{{ route('admin.properties.create') }}">Add property</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Property ID</th><th>Property Name</th><th>Type</th><th>Owner</th><th>Annual Price</th><th></th></tr></thead>
                <tbody>
                @foreach ($properties as $property)
                    <tr>
                        <td>{{ $property->id }}</td>
                        <td>{{ $property->name }}</td>
                        <td>{{ $property->type }}</td>
                        <td>{{ $property->owner?->name }}</td>
                        <td>{{ $property->annual_price }}</td>
                        <td><a href="{{ route('admin.properties.edit', $property) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $properties->links() }}
    </section>
@endsection
