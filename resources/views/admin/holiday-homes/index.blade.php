@extends('layouts.admin')

@section('title', 'Holiday Homes')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <h2>Holiday Home Table</h2>
            <a class="button" href="{{ route('admin.holiday-homes.create') }}">Add holiday home</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Sort</th><th>Photo</th><th>Area Name</th><th>Holiday Home Name</th><th>Bedrooms</th><th>Max Guests</th><th>Status</th><th>Booking Link</th><th></th></tr></thead>
                <tbody>
                @foreach ($holidayHomes as $holidayHome)
                    <tr>
                        <td>{{ $holidayHome->sort_order }}</td>
                        <td>
                            @if ($holidayHome->image_url)
                                <img class="admin-table-thumb" src="{{ $holidayHome->image_url }}" alt="{{ $holidayHome->name }}">
                            @endif
                        </td>
                        <td>{{ $holidayHome->area_name }}</td>
                        <td>{{ $holidayHome->name }}</td>
                        <td>{{ $holidayHome->number_of_bedrooms }}</td>
                        <td>{{ $holidayHome->maximum_number_of_guests }}</td>
                        <td>{{ $holidayHome->is_active ? 'Visible' : 'Hidden' }}</td>
                        <td>@if ($holidayHome->online_booking_link)<a href="{{ $holidayHome->online_booking_link }}" target="_blank">Open</a>@endif</td>
                        <td><a href="{{ route('admin.holiday-homes.edit', $holidayHome) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $holidayHomes->links() }}
    </section>
@endsection
