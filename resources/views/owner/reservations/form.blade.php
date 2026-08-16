<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reservation->exists ? 'Edit Reservation' : 'Add Reservation' }} - Owner Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-shell">
    <aside class="admin-sidebar">
        <a class="brand" href="{{ route('owner.dashboard') }}">Owner Portal</a>
        <nav>
            <a href="{{ route('owner.dashboard') }}#properties">Property List</a>
            <a href="{{ route('owner.dashboard') }}#reservations">Reservation List</a>
            <a href="{{ route('owner.dashboard') }}#activities">Activity List</a>
            <a href="{{ route('home') }}" target="_blank">View Site</a>
        </nav>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <div>
                <p class="eyebrow">Owner Reservation</p>
                <h1>{{ $reservation->exists ? 'Edit Reservation' : 'Add Reservation' }}</h1>
            </div>
            <form method="post" action="{{ route('owner.logout') }}">
                @csrf
                <button class="button ghost" type="submit">Logout</button>
            </form>
        </header>

        @if ($properties->isEmpty())
            <div class="notice">No properties are assigned to this owner yet. Please assign a property in Admin first, then this owner can add reservations for that property.</div>
        @endif

        <form method="post" action="{{ $reservation->exists ? route('owner.reservations.update', $reservation) : route('owner.reservations.store') }}" class="panel cms-form">
            @csrf
            @if ($reservation->exists)
                @method('PUT')
            @endif
            <div class="form-grid">
                <label>Property *
                    <select name="property_id" required>
                        <option value="">Select property</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}" @selected((int) old('property_id', $reservation->property_id) === $property->id)>#{{ $property->id }} - {{ $property->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Number of Guests *<input type="number" min="1" name="number_of_guests" value="{{ old('number_of_guests', $reservation->number_of_guests ?: 1) }}" required></label>
                <label>Check-in Date *<input type="date" name="check_in_date" value="{{ old('check_in_date', optional($reservation->check_in_date)->format('Y-m-d')) }}" required></label>
                <label>Check-out Date *<input type="date" name="check_out_date" value="{{ old('check_out_date', optional($reservation->check_out_date)->format('Y-m-d')) }}" required></label>
                <label>Guest Name *<input name="guest_name" value="{{ old('guest_name', $reservation->guest_name) }}" required></label>
                <label>Telephone Number<input name="telephone" value="{{ old('telephone', $reservation->telephone) }}"></label>
                <label class="wide">Remarks<textarea name="remarks" rows="4">{{ old('remarks', $reservation->remarks) }}</textarea></label>
            </div>
            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
            <div class="form-actions">
                <button class="button" type="submit" @disabled($properties->isEmpty())>{{ $reservation->exists ? 'Update' : 'Create' }}</button>
                <a class="button ghost" href="{{ route('owner.dashboard') }}#reservations">Cancel</a>
            </div>
        </form>

        @if ($reservation->exists)
            <form method="post" action="{{ route('owner.reservations.destroy', $reservation) }}" onsubmit="return confirm('Delete this reservation?')" class="panel">
                @csrf
                @method('DELETE')
                <button class="button danger" type="submit">Delete reservation</button>
            </form>
        @endif
    </main>
</body>
</html>
