<header class="nav">
    <a class="logo" href="{{ url('/') }}">
        <img class="logo-img" src="{{ asset('assets/img/logo.png') }}" alt="Reganto IT Solution">
    </a>
    <nav class="nav-pill" id="navPill" aria-label="Main">
        @foreach($navServices as $s)
            <a href="{{ url('/services/' . $s->slug) }}" class="{{ request()->is('services/' . $s->slug . '*') ? 'active' : '' }}">{{ $s->title }}</a>
        @endforeach
        <a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">About Us</a>
        <a href="{{ url('/team') }}" class="{{ request()->is('team') ? 'active' : '' }}">Team</a>
    </nav>
    <button class="menu-btn" id="menuBtn" aria-label="Menu" type="button">☰</button>
    <a class="lang-sw" href="{{ route('lang', app()->getLocale() === 'bn' ? 'en' : 'bn') }}" aria-label="Switch language" title="{{ app()->getLocale() === 'bn' ? 'Switch to English' : 'বাংলায় দেখুন' }}" hreflang="{{ app()->getLocale() === 'bn' ? 'en' : 'bn' }}">{{ app()->getLocale() === 'bn' ? 'EN' : 'বাংলা' }}</a>
    <a class="btn btn-sm" href="{{ url('/contact') }}">Contact Us <x-icon name="arrow-circle" size="16" /></a>
</header>
