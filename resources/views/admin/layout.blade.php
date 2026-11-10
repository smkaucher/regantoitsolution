<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Dashboard') – Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
</head>
<body class="ad">
@php
    $nav = [
        ['Dashboard', 'admin.dashboard', 'admin'],
        ['Site settings', 'admin.settings', 'settings'],
        ['Services', 'admin.services.*', 'services'],
        ['Projects', 'admin.projects.*', 'projects'],
    ];
@endphp
<aside class="ad-side">
    <a class="ad-brand" href="{{ route('admin.dashboard') }}">Reganto <small>Admin</small></a>
    <nav>
        @foreach($nav as [$label, $route, $match])
            <a href="{{ route(str_ends_with($route, '.*') ? str_replace('.*', '.index', $route) : $route) }}" class="{{ request()->routeIs($route) ? 'on' : '' }}">{{ $label }}</a>
        @endforeach
        <span class="ad-group">Home page content</span>
        @foreach(\App\Support\ContentTypes::all() as $key => $c)
            <a href="{{ route('admin.content.index', $key) }}" class="{{ request()->route('type') === $key ? 'on' : '' }}">{{ $c['label'] }}</a>
        @endforeach
    </nav>
    <div class="ad-foot">
        <a href="{{ url('/') }}" target="_blank" rel="noreferrer">View site ↗</a>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Log out</button></form>
    </div>
</aside>
<main class="ad-main">
    <header class="ad-top"><h1>@yield('title', 'Dashboard')</h1><span>{{ auth()->user()->name }}</span></header>
    @if(session('ok'))<div class="ad-ok">{{ session('ok') }}</div>@endif
    @yield('content')
</main>
</body>
</html>
