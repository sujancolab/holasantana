@extends('layouts.admin')

@section('title', $activity->exists ? 'Edit Activity' : 'Add Activity')

@section('content')
    <form method="post" action="{{ $activity->exists ? route('admin.activities.update', $activity) : route('admin.activities.store') }}" class="panel cms-form">
        @csrf
        @if ($activity->exists)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label>Property *
                <select name="property_id" required>
                    <option value="">Select property</option>
                    @foreach ($properties as $property)
                        <option value="{{ $property->id }}" @selected((int) old('property_id', $activity->property_id) === $property->id)>#{{ $property->id }} - {{ $property->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Visiting Date and Time *<input type="datetime-local" name="visiting_at" value="{{ old('visiting_at', optional($activity->visiting_at)->format('Y-m-d\TH:i')) }}" required></label>
            <label>Visitor Name *<input name="visitor_name" value="{{ old('visitor_name', $activity->visitor_name) }}" required></label>
            <label>Exit Time<input type="time" name="exit_time" value="{{ old('exit_time', $activity->exit_time) }}"></label>
            <label class="wide">Observation<textarea name="observation" rows="3">{{ old('observation', $activity->observation) }}</textarea></label>
            <label class="wide">Activity Performed<textarea name="activity_performed" rows="3">{{ old('activity_performed', $activity->activity_performed) }}</textarea></label>
            <label class="wide">Remarks<textarea name="remarks" rows="4">{{ old('remarks', $activity->remarks) }}</textarea></label>
        </div>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <div class="form-actions">
            <button class="button" type="submit">{{ $activity->exists ? 'Update' : 'Create' }}</button>
            <a class="button ghost" href="{{ route('admin.activities.index') }}">Cancel</a>
        </div>
    </form>
    @if ($activity->exists)
        <form method="post" action="{{ route('admin.activities.destroy', $activity) }}" onsubmit="return confirm('Delete this activity?')" class="panel">
            @csrf
            @method('DELETE')
            <button class="button danger" type="submit">Delete activity</button>
        </form>
    @endif
@endsection
