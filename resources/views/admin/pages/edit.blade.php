@extends('layouts.admin')

@section('title', 'Edit Page')

@section('content')
    @include('admin.pages.partials.form', ['action' => route('admin.pages.update', $page), 'method' => 'put'])
@endsection
