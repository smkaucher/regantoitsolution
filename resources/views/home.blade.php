@extends('layouts.app')

@push('head')
<script type="application/ld+json">{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $site['site_name'] ?? 'Reganto IT Solution',
    'url' => url('/'),
    'logo' => $site['logo'] ?? null,
    'email' => $site['email'] ?? null,
    'telephone' => $site['phone'] ?? null,
    'sameAs' => array_values(array_filter([$site['facebook'] ?? null, $site['instagram'] ?? null, $site['linkedin'] ?? null, $site['youtube'] ?? null, $site['tiktok'] ?? null])),
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    @include('home.hero')
    @include('home.ambition')
    @include('home.logos')
    @include('home.stats')
    @include('home.services')
    @include('home.work')
    @include('home.features')
    @include('home.reviews')
    @include('home.faq')
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/home.js') }}?v={{ filemtime(public_path('assets/js/home.js')) }}" defer></script>
@endpush
