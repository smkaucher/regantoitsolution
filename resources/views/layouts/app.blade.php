<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($site['site_name'] ?? 'Reganto IT Solution') . ' – One studio. Every digital possibility.')</title>
    @php
        $seoTitle = trim($__env->yieldContent('title')) ?: (($site['site_name'] ?? 'Reganto IT Solution') . ' – One studio. Every digital possibility.');
        $seoDesc = trim($__env->yieldContent('description')) ?: ($site['tagline'] ?? '');
        $seoImage = trim($__env->yieldContent('og_image')) ?: ($site['logo'] ?? '');
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if(!empty($site['favicon']) || !empty($site['logo']))
        <link rel="icon" href="{{ $site['favicon'] ?: $site['logo'] }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/site.css') }}?v={{ filemtime(public_path('assets/css/site.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/css/blade.css') }}?v={{ filemtime(public_path('assets/css/blade.css')) }}">
    @stack('head')
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
    <script src="{{ asset('assets/js/site.js') }}?v={{ filemtime(public_path('assets/js/site.js')) }}" defer></script>
    @stack('scripts')
</body>
</html>
