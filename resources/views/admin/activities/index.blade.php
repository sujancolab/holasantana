@extends('layouts.admin')

@section('title', 'Activities')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <h2>Activity List Table</h2>
            <a class="button" href="{{ route('admin.activities.create') }}">Add activity</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Property ID</th><th>Property Name</th><th>Visiting Date and Time</th><th>Visitor</th><th>Exit Time</th><th></th></tr></thead>
                <tbody>
                @foreach ($activities as $activity)
                    <tr>
                        <td>{{ $activity->property_id }}</td>
                        <td>{{ $activity->property?->name }}</td>
                        <td>{{ $activity->visiting_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $activity->visitor_name }}</td>
                        <td>{{ $activity->exit_time }}</td>
                        <td><a href="{{ route('admin.activities.edit', $activity) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $activities->links() }}
    </section>
@endsection
