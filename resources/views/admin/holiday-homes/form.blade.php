@extends('layouts.admin')

@section('title', $holidayHome->exists ? 'Edit Holiday Home' : 'Add Holiday Home')

@section('content')
    <form method="post" action="{{ $holidayHome->exists ? route('admin.holiday-homes.update', $holidayHome) : route('admin.holiday-homes.store') }}" class="panel cms-form" enctype="multipart/form-data">
        @csrf
        @if ($holidayHome->exists)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label>Area Name *<input name="area_name" value="{{ old('area_name', $holidayHome->area_name) }}" required></label>
            <label>Holiday Home Name *<input name="name" value="{{ old('name', $holidayHome->name) }}" required></label>
            <label>Card Photo<input type="file" name="image" accept="image/*"></label>
            <label>Photo URL<input name="image_url" value="{{ old('image_url', $holidayHome->image_url) }}" placeholder="https://..."></label>
            @if ($holidayHome->image_url)
                <div class="wide holiday-home-preview">
                    <img src="{{ $holidayHome->image_url }}" alt="{{ $holidayHome->name }}">
                </div>
            @endif
            <label class="wide">Description<textarea name="description" rows="5">{{ old('description', $holidayHome->description) }}</textarea></label>
            <label>Number of Bedrooms *<input type="number" min="0" name="number_of_bedrooms" value="{{ old('number_of_bedrooms', $holidayHome->number_of_bedrooms ?: 1) }}" required></label>
            <label>Maximum Number of Guests *<input type="number" min="1" name="maximum_number_of_guests" value="{{ old('maximum_number_of_guests', $holidayHome->maximum_number_of_guests ?: 1) }}" required></label>
            <label>Sort Order *<input type="number" min="0" name="sort_order" value="{{ old('sort_order', $holidayHome->sort_order ?? 0) }}" required></label>
            <label class="checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $holidayHome->exists ? $holidayHome->is_active : true))> Show on website</label>
            <label class="wide">Online Booking Link<input type="url" name="online_booking_link" value="{{ old('online_booking_link', $holidayHome->online_booking_link) }}"></label>
        </div>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <div class="form-actions">
            <button class="button" type="submit">{{ $holidayHome->exists ? 'Update' : 'Create' }}</button>
            <a class="button ghost" href="{{ route('admin.holiday-homes.index') }}">Cancel</a>
        </div>
    </form>
    @if ($holidayHome->exists)
        <form method="post" action="{{ route('admin.holiday-homes.destroy', $holidayHome) }}" onsubmit="return confirm('Delete this holiday home?')" class="panel">
            @csrf
            @method('DELETE')
            <button class="button danger" type="submit">Delete holiday home</button>
        </form>
    @endif
@endsection
