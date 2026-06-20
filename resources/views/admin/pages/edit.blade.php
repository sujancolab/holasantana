@extends('layouts.admin')

@section('title', 'Edit Page')

@section('content')
    <section class="panel">
        @include('admin.pages.partials.form', ['action' => route('admin.pages.update', $page), 'method' => 'put'])
    </section>
@endsection
