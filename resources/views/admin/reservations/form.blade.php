@extends('layouts.admin')

@section('title', $reservation->exists ? 'Edit Reservation' : 'Add Reservation')

@section('content')
    <form method="post" action="{{ $reservation->exists ? route('admin.reservations.update', $reservation) : route('admin.reservations.store') }}" class="panel cms-form">
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
            <button class="button" type="submit">{{ $reservation->exists ? 'Update' : 'Create' }}</button>
            <a class="button ghost" href="{{ route('admin.reservations.index') }}">Cancel</a>
        </div>
    </form>
    @if ($reservation->exists)
        <form method="post" action="{{ route('admin.reservations.destroy', $reservation) }}" onsubmit="return confirm('Delete this reservation?')" class="panel">
            @csrf
            @method('DELETE')
            <button class="button danger" type="submit">Delete reservation</button>
        </form>
    @endif
@endsection
