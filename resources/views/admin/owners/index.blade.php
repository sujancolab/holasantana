@extends('layouts.admin')

@section('title', 'Owners')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <h2>Owner Table</h2>
            <a class="button" href="{{ route('admin.owners.create') }}">Add owner</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Owner ID</th><th>Owner Name</th><th>Telephone</th><th>Email</th><th>WhatsApp</th><th>User ID</th><th></th></tr></thead>
                <tbody>
                @foreach ($owners as $owner)
                    <tr>
                        <td>{{ $owner->id }}</td>
                        <td>{{ $owner->name }}</td>
                        <td>{{ $owner->telephone }}</td>
                        <td>{{ $owner->email }}</td>
                        <td>{{ $owner->whatsapp }}</td>
                        <td>{{ $owner->owner_user_id }}</td>
                        <td><a href="{{ route('admin.owners.edit', $owner) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $owners->links() }}
    </section>
@endsection
