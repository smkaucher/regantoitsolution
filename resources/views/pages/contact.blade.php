@extends('layouts.app')
@section('title', 'Contact Us – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Book a service or ask a question: reach us on WhatsApp, email or phone. We reply within 30 minutes.')
@section('content')
    @php $addr = $site['address'] ?: config('site.address'); @endphp
    @include('pages._head', ['title' => 'Contact Us', 'crumb' => 'Contact', 'lead' => 'Book a service or ask us anything. We reply within 30 minutes.'])
    <section class="block">
        <h2>Choose how to reach us</h2>
        @include('partials.contact-actions')
    </section>
    <section class="block">
        <div class="map-card">
            <div class="map-info">
                <span class="map-pin"><x-icon name="rocket" size="22" /></span>
                <h3>Visit our office</h3>
                <p>{{ $addr }}</p>
                <a class="btn btn-sm" href="{{ config('site.map_url') }}" target="_blank" rel="noreferrer">Get directions <x-icon name="arrow-circle" size="16" /></a>
            </div>
            <div class="map-frame">
                <iframe title="Reganto IT Solution on the map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen
                        src="https://maps.google.com/maps?q={{ rawurlencode(config('site.map_query')) }}&z=16&output=embed"></iframe>
            </div>
        </div>
    </section>
@endsection
