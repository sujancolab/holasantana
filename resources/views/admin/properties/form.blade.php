@extends('layouts.admin')

@section('title', $property->exists ? 'Edit Property' : 'Add Property')

@section('content')
    <form method="post" action="{{ $property->exists ? route('admin.properties.update', $property) : route('admin.properties.store') }}" class="panel cms-form">
        @csrf
        @if ($property->exists)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label>Property Name *<input name="name" value="{{ old('name', $property->name) }}" required></label>
            <label>Property Type *
                <select name="type" required>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(old('type', $property->type) === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </label>
            <label>Other Type<input name="other_type" value="{{ old('other_type', $property->other_type) }}"></label>
            <label>Owner *
                <select name="owner_id" required>
                    <option value="">Select owner</option>
                    @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}" @selected((int) old('owner_id', $property->owner_id) === $owner->id)>{{ $owner->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="wide">Address<textarea name="address" rows="3">{{ old('address', $property->address) }}</textarea></label>
            @foreach (['laundry_included' => 'Laundry Included', 'check_in_included' => 'Check-in Included', 'cleaning_included' => 'Cleaning Included', 'management_included' => 'Management Included', 'full_service_included' => 'Full Service Included'] as $field => $label)
                <label class="checkbox"><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $property->{$field}))> {{ $label }} (Yes/No)</label>
            @endforeach
            <label>Price Per Service<input type="number" step="0.01" min="0" name="price_per_service" value="{{ old('price_per_service', $property->price_per_service) }}"></label>
            <label>Annual Price<input type="number" step="0.01" min="0" name="annual_price" value="{{ old('annual_price', $property->annual_price) }}"></label>
            <label class="wide">Remarks<textarea name="remarks" rows="4">{{ old('remarks', $property->remarks) }}</textarea></label>
        </div>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <div class="form-actions">
            <button class="button" type="submit">{{ $property->exists ? 'Update' : 'Create' }}</button>
            <a class="button ghost" href="{{ route('admin.properties.index') }}">Cancel</a>
        </div>
    </form>
    @if ($property->exists)
        <form method="post" action="{{ route('admin.properties.destroy', $property) }}" onsubmit="return confirm('Delete this property?')" class="panel">
            @csrf
            @method('DELETE')
            <button class="button danger" type="submit">Delete property</button>
        </form>
    @endif
@endsection
