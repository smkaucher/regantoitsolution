@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
    <section class="ad-stats">
        @foreach($counts as [$label, $n, $url])
            <a class="ad-stat" href="{{ $url }}"><b>{{ $n }}</b><span>{{ $label }}</span></a>
        @endforeach
    </section>
    <section class="ad-panel">
        <h2>Bookings</h2>
        <p class="ad-empty">Service booking is done by WhatsApp, email or phone call. Update those details in <a href="{{ route('admin.settings') }}">Site settings</a>.</p>
    </section>
@endsection
