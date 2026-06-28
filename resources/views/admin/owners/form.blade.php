@extends('layouts.admin')

@section('title', $owner->exists ? 'Edit Owner' : 'Add Owner')

@section('content')
    <form method="post" action="{{ $owner->exists ? route('admin.owners.update', $owner) : route('admin.owners.store') }}" class="panel cms-form">
        @csrf
        @if ($owner->exists)
            @method('PUT')
        @endif
        <div class="form-grid">
            <label>Owner Name *<input name="name" value="{{ old('name', $owner->name) }}" required></label>
            <label>Telephone Number<input name="telephone" value="{{ old('telephone', $owner->telephone) }}"></label>
            <label>Email Address<input type="email" name="email" value="{{ old('email', $owner->email) }}"></label>
            <label>WhatsApp Number<input name="whatsapp" value="{{ old('whatsapp', $owner->whatsapp) }}"></label>
            <label class="wide">Google Photo Album Link<input type="url" name="google_photo_album_link" value="{{ old('google_photo_album_link', $owner->google_photo_album_link) }}"></label>
            <label>Owner User ID *<input name="owner_user_id" value="{{ old('owner_user_id', $owner->owner_user_id) }}" required></label>
            <label>Owner Password {{ $owner->exists ? '(leave blank to keep current)' : '*' }}<input type="password" name="owner_password" @required(! $owner->exists)></label>
        </div>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <div class="form-actions">
            <button class="button" type="submit">{{ $owner->exists ? 'Update' : 'Create' }}</button>
            <a class="button ghost" href="{{ route('admin.owners.index') }}">Cancel</a>
        </div>
    </form>
    @if ($owner->exists)
        <form method="post" action="{{ route('admin.owners.destroy', $owner) }}" onsubmit="return confirm('Delete this owner?')" class="panel">
            @csrf
            @method('DELETE')
            <button class="button danger" type="submit">Delete owner</button>
        </form>
    @endif
@endsection
