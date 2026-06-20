@extends('layouts.admin')

@section('title', 'Add Page')

@section('content')
    @include('admin.pages.partials.form', ['action' => route('admin.pages.store'), 'method' => 'post'])
@endsection
