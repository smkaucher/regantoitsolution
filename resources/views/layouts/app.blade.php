<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($site['site_name'] ?? 'Reganto IT Solution') . ' – One studio. Every digital possibility.')</title>
    @php
        $seoTitle = trim($__env->yieldContent('title')) ?: (($site['site_name'] ?? 'Reganto IT Solution') . ' – One studio. Every digital possibility.');
        $seoDesc = trim($__env->yieldContent('description')) ?: ($site['tagline'] ?? '');
        $seoImage = trim($__env->yieldContent('og_image')) ?: asset('assets/img/logo.png');
        $seoUrl = url()->current();
    @endphp
    <meta name="description" content="{{ Str::limit(strip_tags($seoDesc), 160) }}">
    <link rel="canonical" href="{{ $seoUrl }}">
    <meta property="og:site_name" content="{{ $site['site_name'] ?? 'Reganto IT Solution' }}">
    <meta property="og:type" content="{{ trim($__env->yieldContent('og_type')) ?: 'website' }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($seoDesc), 200) }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    @if($seoImage)<meta property="og:image" content="{{ $seoImage }}">@endif
    <meta name="twitter:card" content="{{ $seoImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($seoDesc), 200) }}">
    @if($seoImage)<meta name="twitter:image" content="{{ $seoImage }}">@endif
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v={{ filemtime(public_path('assets/css/site.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/blade.css') }}?v={{ filemtime(public_path('assets/css/blade.css')) }}">
    @stack('head')
    @php
        // Texts the page scripts show. Scripts are not translated automatically, so their texts are looked up here.
        $jsText = ['Client', 'Category', 'Loading…',
            "Hi! I'm Reganto AI. Ask me about our services, projects, team, pricing, location or how to book.",
            'Sorry, I could not answer right now. Please message us on WhatsApp or use the Contact page.',
            // Home "Write a review" form (home.js)
            'Sending…', 'Please enter your name.', 'Please write your review.', 'Please write a little more (at least 10 letters).',
            'The photo must be smaller than 2 MB.', 'Thank you! Your review will show on the site after our team checks it.',
            'Too many tries. Please wait a few minutes and try again.', 'This page was open too long. Please reload the page and try again.',
            'Could not send your review. Please try again.', 'Could not send your review. Please check your internet and try again.',
            // /home2 laptop screen (home2.js)
            'Our work', 'Why us', 'Reviews', 'FAQ'];
        $jsText = collect($jsText)->mapWithKeys(fn ($k) => [$k => app()->getLocale() === 'bn' ? \App\Support\Translator::text($k) : $k]);
    @endphp
    <script>window.T = {!! json_encode($jsText, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}; window.t = function (k) { return (window.T && window.T[k]) || k; };</script>
</head>
<body>
    @include('partials.announce')
    <div class="page" id="top">
        @include('partials.navbar')
        <main>
            @yield('content')
        </main>
        @include('partials.footer')
    </div>
    @include('partials.chat')
    @include('partials.ai-chat')
    <script src="{{ asset('assets/js/site.js') }}?v={{ filemtime(public_path('assets/js/site.js')) }}" defer></script>
    @stack('scripts')
</body>
</html>
