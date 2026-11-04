<header class="nav">
    <a class="logo" href="{{ url('/') }}">
        @if(!empty($site['logo']))
            <img class="logo-img" src="{{ $site['logo'] }}" alt="{{ $site['site_name'] ?? '' }}">
        @else
            {{ $site['site_name'] ?? 'Reganto' }}
        @endif
    </a>
    <nav class="nav-pill" id="navPill" aria-label="Main">
        <a href="{{ url('/projects') }}" class="{{ request()->is('projects*') ? 'active' : '' }}">Projects</a>
        <div class="has-dd" data-dd>
            <a href="{{ url('/services') }}" class="{{ request()->is('services*') ? 'active' : '' }}">Services <x-icon name="chevron" size="14" style="transform:rotate(90deg)" /></a>
            <div class="dd" hidden>
                @foreach($navServices as $s)
                    <div>
                        <a class="dd-title" href="{{ url('/services/' . $s->slug) }}">{{ $s->title }}</a>
                        @foreach($s->items as $i)
                            <a href="{{ url('/services/' . $s->slug) }}">{{ $i->title }}</a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        <a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">About Us</a>
        <a href="{{ url('/team') }}" class="{{ request()->is('team') ? 'active' : '' }}">Team</a>
        <a href="{{ url('/blog') }}" class="{{ request()->is('blog*') ? 'active' : '' }}">Blogs</a>
    </nav>
    <button class="menu-btn" id="menuBtn" aria-label="Menu" type="button">☰</button>
    <a class="btn btn-sm" href="{{ url('/contact') }}">Contact Us <x-icon name="arrow-circle" size="16" /></a>
</header>
