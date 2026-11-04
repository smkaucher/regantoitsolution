@extends('layouts.app')
@section('title', 'Page not found – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@push('head')<meta name="robots" content="noindex">@endpush
@section('content')
    <section class="pg-head">
        <small class="pg-crumb">Error 404</small>
        <h1>Page not found</h1>
        <p>The page you are looking for does not exist or has moved.</p>
    </section>
    <section class="block center">
        <a class="btn" href="{{ url('/') }}">Back to home <x-icon name="arrow-right" /></a>
        <a class="btn ghost-l" href="{{ url('/contact') }}">Contact us</a>
    </section>
@endsection
