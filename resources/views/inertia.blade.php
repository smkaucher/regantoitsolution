{{--
    Root page of the React (Inertia) public pages. The browser gets this HTML on the first visit only; after that
    Inertia swaps pages without a reload. The SEO tags are written here from the page's "meta" prop, so search
    engines and link previews (Facebook, WhatsApp) see them without running any JavaScript. They carry the
    "data-inertia" attribute, so React's <Head> replaces them (instead of adding duplicates) on later page changes.
--}}
@php
    $meta = $page['props']['meta'] ?? [];
    $siteName = $page['props']['site']['site_name'] ?? 'Reganto IT Solution';
    $seoTitle = $meta['title'] ?? ($siteName . ' – One studio. Every digital possibility.');
    $seoDesc = Str::limit(strip_tags($meta['description'] ?? ($page['props']['site']['tagline'] ?? '')), 160);
    $seoDescLong = Str::limit(strip_tags($meta['description'] ?? ($page['props']['site']['tagline'] ?? '')), 200);
    $seoImage = $meta['image'] ?? asset('assets/img/logo.png');
    $seoUrl = url()->current();
    $bn = app()->getLocale() === 'bn';
    $css = fn ($f) => asset('assets/css/' . $f) . '?v=' . filemtime(public_path('assets/css/' . $f));
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-inertia>{{ $seoTitle }}</title>
    <meta data-inertia="description" name="description" content="{{ $seoDesc }}">
    @if(!empty($meta['robots']))<meta data-inertia="robots" name="robots" content="{{ $meta['robots'] }}">@endif
    <link data-inertia="canonical" rel="canonical" href="{{ $seoUrl }}">
    <meta data-inertia="og:site_name" property="og:site_name" content="{{ $siteName }}">
    <meta data-inertia="og:type" property="og:type" content="{{ $meta['type'] ?? 'website' }}">
    <meta data-inertia="og:title" property="og:title" content="{{ $seoTitle }}">
    <meta data-inertia="og:description" property="og:description" content="{{ $seoDescLong }}">
    <meta data-inertia="og:url" property="og:url" content="{{ $seoUrl }}">
    <meta data-inertia="og:image" property="og:image" content="{{ $seoImage }}">
    <meta data-inertia="twitter:card" name="twitter:card" content="summary_large_image">
    <meta data-inertia="twitter:title" name="twitter:title" content="{{ $seoTitle }}">
    <meta data-inertia="twitter:description" name="twitter:description" content="{{ $seoDescLong }}">
    <meta data-inertia="twitter:image" name="twitter:image" content="{{ $seoImage }}">
    @if(!empty($meta['jsonLd']))<script data-inertia="json-ld" type="application/ld+json">{!! json_encode($meta['jsonLd'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>@endif
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}">
    {{-- The same stylesheets as the Blade pages, unchanged, so the React pages look exactly the same --}}
    <link rel="stylesheet" href="{{ $css('site.css') }}">
    <link rel="stylesheet" href="{{ $css('blade.css') }}">
    <link rel="stylesheet" href="{{ $css('home2.css') }}">
    @php
        // Texts the scripts show (the same list as layouts/app.blade.php), plus, for a Bangla visitor, the whole
        // English -> Bangla word list: React pages are translated in the browser (resources/js/lib/translate.js),
        // because Inertia sends page data as JSON, which the Localize middleware cannot translate.
        $jsText = ['Client', 'Category', 'Loading…', 'Tap to load this section',
            "Hi! I'm Reganto AI. Ask me about our services, projects, team, pricing, location or how to book.",
            'Sorry, I could not answer right now. Please message us on WhatsApp or use the Contact page.',
            'Sending…', 'Please enter your name.', 'Please write your review.', 'Please write a little more (at least 10 letters).',
            'The photo must be smaller than 2 MB.', 'Thank you! Your review will show on the site after our team checks it.',
            'Too many tries. Please wait a few minutes and try again.', 'This page was open too long. Please reload the page and try again.',
            'Could not send your review. Please try again.', 'Could not send your review. Please check your internet and try again.',
            'Our work', 'Why us', 'Reviews', 'FAQ'];
        $jsText = collect($jsText)->mapWithKeys(fn ($k) => [$k => $bn ? \App\Support\Translator::text($k) : $k]);
    @endphp
    <script>window.T = {!! json_encode($jsText, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}; window.t = function (k) { return (window.T && window.T[k]) || (window.BN_T ? window.BN_T(k) : k); };</script>
    @if($bn)<script>window.BN = {!! json_encode(\App\Http\Middleware\HandleInertiaRequests::dictionary(), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!};</script>@endif
    @vite('resources/js/app.jsx')
</head>
<body>
    @inertia
</body>
</html>
