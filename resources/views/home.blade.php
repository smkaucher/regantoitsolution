@extends('layouts.app')

@push('head')
<script type="application/ld+json">{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $site['site_name'] ?? 'Reganto IT Solution',
    'url' => url('/'),
    'logo' => asset('assets/img/logo.png'),
    'email' => $site['email'] ?? null,
    'telephone' => $site['phone'] ?? null,
    'sameAs' => array_values(array_filter([$site['facebook'] ?? null, $site['instagram'] ?? null, $site['linkedin'] ?? null, $site['youtube'] ?? null, $site['tiktok'] ?? null])),
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    @include('home.hero')
    @include('home.logos')

    {{-- Everything below loads when the visitor scrolls close to it, so the first screen is small and fast. --}}
    @foreach([['services', 44], ['work', 36], ['features', 30], ['reviews', 28], ['faq', 26]] as [$part, $height])
        <div id="{{ $part }}" class="lazy-ph" data-lazy="{{ route('home.section', $part) }}?v={{ \App\Support\SiteVersion::get() }}" style="--h:{{ $height }}rem" aria-busy="true"></div>
    @endforeach
    <noscript>
        <p class="lazy-fail"><a class="btn btn-sm" href="{{ url('/services') }}">Our Services</a> <a class="btn btn-sm" href="{{ url('/projects') }}">Our Projects</a></p>
    </noscript>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/home.js') }}?v={{ filemtime(public_path('assets/js/home.js')) }}" defer></script>
@endpush
