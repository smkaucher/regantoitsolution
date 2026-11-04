@php
    $prompts = ['Design a premium digital experience for a growing brand...', 'Build a SaaS dashboard that converts...', 'Create a brand identity that stands out...'];
@endphp
<section class="hero" id="product" data-replay data-threshold="0.25">
    <div class="hero-glow g1" aria-hidden="true"></div>
    <div class="hero-glow g2" aria-hidden="true"></div>

    <div class="hero-copy">
        <span class="eyebrow" data-a="up" style="--d:.05s"><i class="eyebrow-dot"></i> DIGITAL STUDIO · {{ date('Y') }}</span>
        <h1>
            <span class="hl"><span data-a="line" style="--d:.17s">One studio.</span></span>
            <span class="hl"><span data-a="line" style="--d:.29s">Every <em class="shine">digital</em></span></span>
            <span class="hl"><span data-a="line" style="--d:.41s">possibility.</span></span>
        </h1>
        <p data-a="up" style="--d:.53s">{{ $site['hero_subtitle'] ?? 'We design and build high-performance websites, digital products, SaaS platforms and brands that make businesses impossible to ignore.' }}</p>
        <div class="hero-cta" data-a="pop" style="--d:.65s">
            <a class="btn" href="{{ url('/contact') }}">Start a project <x-icon name="arrow-circle" /></a>
            <a class="btn ghost" href="#work">Explore our work <span class="bob">↓</span></a>
        </div>
        <div class="ai-box" data-a="up" style="--d:.77s">
            <span class="ai-spark">✦</span> <b>REGANTO AI CREATIVE ASSIST</b>
            <span class="ai-typed" data-typing='@json($prompts)' data-caret="ai-caret"></span>
            <span class="ai-go">↗</span>
        </div>
    </div>

    <div class="hero-rings" aria-hidden="true"><i></i></div>

    <div class="hero-visual">
        <div class="hero-arch" data-a="arch" style="--d:.25s">
            <img class="hero-photo" src="{{ asset('assets/img/hero.png') }}" alt="" aria-hidden="true" onerror="this.style.display='none'">
        </div>
    </div>
</section>
