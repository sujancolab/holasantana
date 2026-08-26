<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Owner Dashboard - Hola Santana</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell">
    <aside class="admin-sidebar">
        <a class="brand" href="{{ route('owner.dashboard') }}">Owner Portal</a>
        <nav>
            <a href="#properties">Property List</a>
            <a href="#reservations">Reservation List</a>
            <a href="#activities">Activity List</a>
            <a href="{{ route('home') }}" target="_blank">View Site</a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">Welcome</p>
                <h1>{{ $owner->name }}</h1>
            </div>
            <form method="post" action="{{ route('owner.logout') }}">
                @csrf
                <button class="button ghost" type="submit">Logout</button>
            </form>
        </header>
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif
        @if (filled($owner->google_activity_list_link))
            <section class="panel owner-link-panel">
                <div class="panel-head"><h2>Owner Links</h2></div>
                <div class="owner-link-actions">
                    <a class="button" href="{{ $owner->google_activity_list_link }}" target="_blank" rel="noopener">Work-sheet</a>
                </div>
            </section>
        @endif
        <section class="panel" id="properties">
            <div class="panel-head"><h2>Property List</h2></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Address</th><th>Google photo link</th><th>Services</th><th>Remarks</th></tr></thead>
                    <tbody>
                    @foreach ($properties as $property)
                        <tr>
                            <td>{{ $property->id }}</td>
                            <td>{{ $property->name }}</td>
                            <td>{{ $property->type }}</td>
                            <td>{{ $property->address }}</td>
                            <td>
                                @if (filled($property->google_photo_link))
                                    <a class="button ghost" href="{{ $property->google_photo_link }}" target="_blank" rel="noopener">Open photos</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ collect([
                                    $property->laundry_included ? 'Laundry' : null,
                                    $property->check_in_included ? 'Check-in' : null,
                                    $property->cleaning_included ? 'Cleaning' : null,
                                    $property->management_included ? 'Management' : null,
                                    $property->full_service_included ? 'Full Service' : null,
                                ])->filter()->join(', ') }}
                            </td>
                            <td>{{ $property->remarks }}</td>
                        </tr>
                    @endforeach
                    @if ($properties->isEmpty())
                        <tr>
                            <td colspan="7">No properties assigned to this owner yet. Admin must assign a property before reservations can be created.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </section>
        <section class="panel" id="reservations">
            <div class="panel-head">
                <h2>Reservation List</h2>
                <a class="button" href="{{ route('owner.reservations.create') }}">Add reservation</a>
            </div>
            @if ($properties->isEmpty())
                <div class="notice">Reservation actions are visible here, but this owner needs at least one assigned property before adding a reservation.</div>
            @endif
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Property</th><th>Check-in</th><th>Check-out</th><th>Guests</th><th>Guest</th><th>Telephone</th><th>Remarks</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($reservations as $reservation)
                        <tr>
                            <td>{{ $reservation->property?->name }}</td>
                            <td>{{ $reservation->check_in_date?->format('Y-m-d') }}</td>
                            <td>{{ $reservation->check_out_date?->format('Y-m-d') }}</td>
                            <td>{{ $reservation->number_of_guests }}</td>
                            <td>{{ $reservation->guest_name }}</td>
                            <td>{{ $reservation->telephone }}</td>
                            <td>{{ $reservation->remarks }}</td>
                            <td><a href="{{ route('owner.reservations.edit', $reservation) }}">Edit</a></td>
                        </tr>
                    @endforeach
                    @if ($reservations->isEmpty())
                        <tr>
                            <td colspan="8">No reservations yet. After a reservation is created, the Edit link will appear in this table.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </section>
        <section class="panel" id="activities">
            <div class="panel-head"><h2>Activity List</h2></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Property</th><th>Visiting Date and Time</th><th>Visitor</th><th>Observation</th><th>Activity</th><th>Exit</th><th>Remarks</th></tr></thead>
                    <tbody>
                    @foreach ($activities as $activity)
                        <tr>
                            <td>{{ $activity->property?->name }}</td>
                            <td>{{ $activity->visiting_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $activity->visitor_name }}</td>
                            <td>{{ $activity->observation }}</td>
                            <td>{{ $activity->activity_performed }}</td>
                            <td>{{ $activity->exit_time }}</td>
                            <td>{{ $activity->remarks }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
