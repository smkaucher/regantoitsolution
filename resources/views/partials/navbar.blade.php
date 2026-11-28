@php $bn = app()->getLocale() === 'bn'; @endphp
{{--
    Floating light glass bar, the same on every page and when scrolled. On the home page it sits over the hero.
--}}
{{-- Over the hero (no gap) on the home page and on /home2; rg-hd--h2 keeps its links dark over /home2's light photo --}}
<header class="rg-hd {{ request()->is('/') ? 'rg-hd--over' : (request()->is('home2') ? 'rg-hd--over rg-hd--h2' : '') }}" id="rgHd">
    <div class="rg-in">
        <a class="rg-logo" href="{{ url('/') }}">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Reganto IT Solution" width="300" height="100">
        </a>

        <nav class="rg-nav" id="rgNav" aria-label="Main">
            @foreach($navServices as $s)
                <a class="rg-lk {{ request()->is('services/' . $s->slug . '*') ? 'on' : '' }}" href="{{ url('/services/' . $s->slug) }}">{{ $s->title }}</a>
            @endforeach
            <a class="rg-lk rg-lk--blue {{ request()->is('about') ? 'on' : '' }}" href="{{ url('/about') }}">About Us</a>
            <a class="rg-lk rg-lk--blue {{ request()->is('team') ? 'on' : '' }}" href="{{ url('/team') }}">Team</a>
        </nav>

        <div class="rg-act">
            <a class="rg-lang" href="{{ route('lang', $bn ? 'en' : 'bn') }}" aria-label="Switch language" title="{{ $bn ? 'Switch to English' : 'বাংলায় দেখুন' }}" hreflang="{{ $bn ? 'en' : 'bn' }}">{{ $bn ? 'EN' : 'বাংলা' }}</a>
            <a class="rg-quote" href="{{ url('/contact') }}#brief">Start a project <x-icon name="arrow-circle" size="16" /></a>
            <button type="button" class="rg-menu" id="rgMenu" aria-label="Menu" aria-expanded="false" aria-controls="rgNav"><span></span></button>
        </div>
    </div>
</header>
