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
        <section class="panel" id="properties">
            <div class="panel-head"><h2>Property List</h2></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>ID</th><th>Name</th><th>Type</th><th>Address</th><th>Services</th><th>Remarks</th></tr></thead>
                    <tbody>
                    @foreach ($properties as $property)
                        <tr>
                            <td>{{ $property->id }}</td>
                            <td>{{ $property->name }}</td>
                            <td>{{ $property->type }}</td>
                            <td>{{ $property->address }}</td>
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
                    </tbody>
                </table>
            </div>
        </section>
        <section class="panel" id="reservations">
            <div class="panel-head"><h2>Reservation List</h2></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Property</th><th>Check-in</th><th>Check-out</th><th>Guests</th><th>Guest</th><th>Telephone</th><th>Remarks</th></tr></thead>
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
                        </tr>
                    @endforeach
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
