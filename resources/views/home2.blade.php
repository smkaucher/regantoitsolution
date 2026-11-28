@extends('layouts.app')
@section('title', ($site['site_name'] ?? 'Reganto IT Solution') . ' – One studio. Every digital possibility.')

@push('head')
    {{-- A second version of the home page: kept out of search results so the real home page is the one indexed --}}
    <meta name="robots" content="noindex,follow">
    <link rel="stylesheet" href="{{ asset('assets/css/home2.css') }}?v={{ filemtime(public_path('assets/css/home2.css')) }}">
    {{-- Three.js for the 3D laptop, pinned to one version. Only desktops download it (home2.js loads home2-3d.js there). --}}
    <script type="importmap">{"imports":{"three":"https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.module.min.js","three/addons/":"https://cdn.jsdelivr.net/npm/three@0.160.0/examples/jsm/"}}</script>
@endpush

@section('content')
@php
    $pad = fn ($i) => str_pad($i, 2, '0', STR_PAD_LEFT);
    $stats = [[80, '+', 0, 'Projects Delivered'], [40, '+', 0, 'Happy Clients'], [4.9, '★', 1, 'Client Rating'], [24, '/7', 0, 'Digital Support']];
    // Icon and small picture per service (matched on the slug; anything else gets the chart)
    $kind = function ($slug) {
        foreach (['develop' => 'code', 'design' => 'design', 'brand' => 'design', 'market' => 'chart', 'secur' => 'shield', 'social' => 'social'] as $needle => $k) {
            if (str_contains($slug, $needle)) return $k;
        }
        return 'chart';
    };
    $icons = [
        'code' => '<path d="M8 7 3 12l5 5M16 7l5 5-5 5M14 4l-4 16"/>',
        'design' => '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.6-.8 1.6-1.6 0-.5-.2-.8-.5-1.1-.3-.3-.5-.7-.5-1.1 0-.9.7-1.6 1.6-1.6H16a5 5 0 0 0 5-5c0-4-4-7.6-9-7.6Z"/><circle cx="7.5" cy="11" r="1.2"/><circle cx="10.5" cy="7.2" r="1.2"/><circle cx="15" cy="7.6" r="1.2"/>',
        'chart' => '<path d="M3 11v3a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1Z"/><path d="M15 9a4 4 0 0 1 0 6M18 6a8 8 0 0 1 0 12"/>',
        'shield' => '<path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/><rect x="9" y="11" width="6" height="5" rx="1"/><path d="M10 11V9.5a2 2 0 0 1 4 0V11"/>',
        'social' => '<circle cx="12" cy="8" r="3"/><circle cx="5" cy="10" r="2.2"/><circle cx="19" cy="10" r="2.2"/><path d="M6.5 19a5.5 5.5 0 0 1 11 0M1.8 18a3.4 3.4 0 0 1 5-2.9M22.2 18a3.4 3.4 0 0 0-5-2.9"/>',
    ];
@endphp
<div class="h2" data-h2>

    {{-- ================= Hero: copy on the left; the laptop opens on the right and five service monitors float up ================= --}}
    <section class="h2-hero" data-h2-hero>
        <div class="h2-bg" aria-hidden="true"><i class="h2-sweep"></i></div>

        <div class="h2-copy">
            <p class="h2-eb"><i></i>DIGITAL STUDIO · {{ date('Y') }}</p>
            <h1 class="h2-title">
                <span><b>One studio.</b></span>
                <span><b>Every <em>digital</em></b></span>
                <span><b>possibility.</b></span>
            </h1>
            <p class="h2-sub">{{ $site['hero_subtitle'] ?? 'Software, UI/UX design, branding, marketing and cyber security for startups and growing companies.' }}</p>
            <div class="h2-cta">
                <a class="hm-btn" href="{{ url('/contact') }}#brief">Start a project <span><x-icon name="arrow-right" size="14" /></span></a>
                <a class="h2-ghost" href="#h2-services">See what we do</a>
            </div>
            <div class="h2-stats" data-replay>
                @foreach($stats as [$value, $suffix, $decimals, $label])
                    <div><strong><span data-count="{{ $value }}" data-suffix="{{ $suffix }}" data-decimals="{{ $decimals }}">0</span></strong><small>{{ $label }}</small></div>
                @endforeach
            </div>
        </div>

        <div class="h2-stage" data-h2-stage>
            {{-- Coverflow like the reviews deck: five glass screens, the laptop screen's size, two on each side of the
                 laptop. Each step slides the row one place; the one reaching the middle slides into the laptop's
                 screen (which then shows it). home2.js sets --d (place: -2..2) on each screen. --}}
            <div class="cfv" aria-label="Our services">
                @foreach($heroServices as $i => $s)
                    @php $k = $kind($s->slug); @endphp
                    <a class="cv gbox" data-cv href="{{ url('/services/' . $s->slug) }}" style="--d:{{ $i }}">
                        <span class="sl on">
                            <span class="sl-txt">
                                <svg class="gp-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$k] !!}</svg>
                                <strong>{{ $s->title }}</strong>
                                @if($s->short_description)<small>{{ $s->short_description }}</small>@endif
                                <span class="gp-btn">Learn More <x-icon name="arrow-right" size="10" /></span>
                            </span>
                            <span class="sl-art">
                                <span class="gp-art" data-art="{{ $k }}" aria-hidden="true">
                                    @switch($k)
                                        @case('code')<span class="a-win"><i></i><i></i><i></i><i></i><i></i></span>@break
                                        @case('design')<span class="a-scr a"></span><span class="a-scr b"></span><span class="a-dots"><i></i><i></i><i></i></span>@break
                                        @case('shield')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">{!! $icons['shield'] !!}</svg><span class="a-net"></span>@break
                                        @case('social')<span class="a-soc"><x-icon name="facebook" size="12" /><x-icon name="instagram" size="12" /><x-icon name="youtube" size="12" /><x-icon name="linkedin" size="12" /><x-icon name="tiktok" size="12" /></span>@break
                                        @default<span class="a-bars"><i style="--h:30%"></i><i style="--h:45%"></i><i style="--h:40%"></i><i style="--h:62%"></i><i style="--h:78%"></i><i style="--h:96%"></i></span>
                                    @endswitch
                                </span>
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
            <div class="h2-lslot" data-dv-slot="hero" aria-hidden="true"></div>
        </div>
    </section>

    @include('home.logos')

    {{-- ================= Services: the same pinned section; the monitor docks on its left (svs-mslot) ================= --}}
    <div id="h2-services">
        @include('home.services', ['services' => $services, 'monitorSlot' => true])
    </div>

    {{-- ================= The rest loads as the visitor scrolls, exactly as on the home page ================= --}}
    @foreach([['work', 36], ['features', 30], ['reviews', 28], ['faq', 26]] as [$part, $height])
        <div id="{{ $part }}" class="lazy-ph" data-lazy="{{ route('home.section', $part) }}?v={{ \App\Support\SiteVersion::get() }}" style="--h:{{ $height }}rem" aria-busy="true" data-h2-part="{{ $part }}"></div>
    @endforeach

    {{-- ================= Landing spot: the laptop arrives here above the footer ================= --}}
    <section class="h2-dock" data-h2-dock>
        <div class="h2-dock-copy">
            <p class="h2-eb"><i></i>YOUR TURN</p>
            <h2>Your project could be <em>on this screen next.</em></h2>
            <p>Tell us what you want to build. We read every brief and reply within 30 minutes.</p>
            <div class="h2-cta">
                <a class="hm-btn" href="{{ url('/contact') }}#brief">Start a project <span><x-icon name="arrow-right" size="14" /></span></a>
                @if(!empty($site['whatsapp']))<a class="h2-ghost" href="{{ $site['whatsapp'] }}" target="_blank" rel="noreferrer">Chat on WhatsApp</a>@endif
            </div>
        </div>
        <div class="h2-dslot" data-dv-slot="dock" aria-hidden="true"></div>
    </section>
</div>

{{-- ================= The travelling laptop (home2.js moves it between the slots above) ================= --}}
<div class="dv" id="dv" aria-hidden="true">
    <div class="dv-3d">
        <div class="dv-lid">
            <div class="dv-bezel">
                <i class="dv-cam"></i>
                <div class="dv-screen">
                    {{-- 1. Hero: the current service of the carousel (the glass screens show the one before and after) --}}
                    <div class="dv-scr" data-scr="site" data-sl-track="0">
                        @foreach($heroServices as $i => $s)
                            @php $k = $kind($s->slug); @endphp
                            <article class="ls" data-sl-card>
                                <div class="ls-txt">
                                    <span class="ls-num">{{ $pad($i + 1) }} / {{ $pad($heroServices->count()) }}</span>
                                    <h3>{{ $s->title }}</h3>
                                    @if($s->short_description)<p>{{ $s->short_description }}</p>@endif
                                    <ul>@foreach($s->items->take(3) as $item)<li>{{ $item->title }}</li>@endforeach</ul>
                                    <span class="ls-go">View service <x-icon name="arrow-right" size="12" /></span>
                                </div>
                                <div class="ls-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$k] !!}</svg></div>
                            </article>
                        @endforeach
                        <div class="ls-dots">@foreach($heroServices as $i => $s)<i data-sl-dot></i>@endforeach</div>
                    </div>

                    {{-- 2. Services: one panel per category, switched in step with the pinned section --}}
                    <div class="dv-scr" data-scr="svc">
                        <div class="sv-bar"><i></i><i></i><i></i><span>Our services</span><b data-sv-count>01 / {{ $pad($services->count()) }}</b></div>
                        @foreach($services as $i => $s)
                            <article class="sv-panel {{ $i === 0 ? 'on' : '' }}" data-dv-panel>
                                <span class="sv-num">{{ $pad($i + 1) }}</span>
                                <h3>{{ $s->title }}</h3>
                                @if($s->short_description)<p>{{ $s->short_description }}</p>@endif
                                <ul>@foreach($s->items->take(4) as $item)<li>{{ $item->title }}</li>@endforeach</ul>
                                <span class="sv-foot"><b>{{ $s->items_count }}+</b> services in this category</span>
                            </article>
                        @endforeach
                        <div class="sv-progress"><i data-sv-progress></i></div>
                    </div>
                    {{-- 3. On the way down: the section being read --}}
                    <div class="dv-scr" data-scr="mini">
                        <span class="mi-k">You're viewing</span>
                        <strong data-mini-label>Our work</strong>
                        <span class="mi-bar"><i data-mini-progress></i></span>
                    </div>
                    {{-- 4. Landed: start a project --}}
                    <div class="dv-scr" data-scr="cta">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="" width="300" height="100">
                        <strong>Let's build yours.</strong>
                        <span class="ct-line"></span><span class="ct-line short"></span><span class="ct-line"></span>
                        <b class="ct-btn">Start a project</b>
                    </div>
                </div>
            </div>
        </div>
        <div class="dv-base"><div class="dv-deck"><span class="dv-keys"></span><span class="dv-pad"></span></div><div class="dv-lip"><i></i></div></div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/home.js') }}?v={{ filemtime(public_path('assets/js/home.js')) }}" defer></script>
    <script src="{{ asset('assets/js/home2.js') }}?v={{ filemtime(public_path('assets/js/home2.js')) }}" defer></script>
    <script>window.H2_3D_SRC = @json(asset('assets/js/home2-3d.js') . '?v=' . filemtime(public_path('assets/js/home2-3d.js')));</script>
@endpush
