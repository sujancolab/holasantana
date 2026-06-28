@extends('layouts.admin')

@section('title', 'Reservations')

@section('content')
    <section class="panel">
        <div class="panel-head">
            <h2>Property Reservation Table</h2>
            <a class="button" href="{{ route('admin.reservations.create') }}">Add reservation</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Property ID</th><th>Property Name</th><th>Check-in</th><th>Check-out</th><th>Guests</th><th>Guest Name</th><th>Telephone</th><th></th></tr></thead>
                <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td>{{ $reservation->property_id }}</td>
                        <td>{{ $reservation->property?->name }}</td>
                        <td>{{ $reservation->check_in_date?->format('Y-m-d') }}</td>
                        <td>{{ $reservation->check_out_date?->format('Y-m-d') }}</td>
                        <td>{{ $reservation->number_of_guests }}</td>
                        <td>{{ $reservation->guest_name }}</td>
                        <td>{{ $reservation->telephone }}</td>
                        <td><a href="{{ route('admin.reservations.edit', $reservation) }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $reservations->links() }}
    </section>
@endsection
