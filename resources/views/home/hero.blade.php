@php
    $prompts = ['What services do you offer?', 'Show me your recent projects', 'How can I book a service?', 'Where is your office?'];
@endphp
<section class="hero" id="product" data-replay data-threshold="0.25">
    <div class="hero-glow g1" aria-hidden="true"></div>
    <div class="hero-glow g2" aria-hidden="true"></div>

    <div class="hero-copy">
        <span class="eyebrow" data-a="up" style="--d:.05s">DIGITAL STUDIO · {{ date('Y') }}</span>
        <h1>
            <span class="hl"><span data-a="line" style="--d:.17s">One studio.</span></span>
            <span class="hl"><span data-a="line" style="--d:.29s">Every <em class="shine">digital</em></span></span>
            <span class="hl"><span data-a="line" style="--d:.41s">possibility.</span></span>
        </h1>
        <p data-a="up" style="--d:.53s">{{ $site['hero_subtitle'] ?? 'Software, UI/UX design, branding, marketing and cyber security for startups and growing companies.' }}</p>
        <div class="hero-cta" data-a="pop" style="--d:.65s">
            <a class="btn" href="{{ url('/contact') }}">Start a project <x-icon name="arrow-circle" /></a>
            <a class="btn ghost" href="#work">Explore our work <span class="bob">↓</span></a>
        </div>
        <button type="button" class="aib" data-ai-open data-a="up" style="--d:.77s" aria-label="Open Reganto AI chat">
            <span class="aib-field">
                <span class="ai-typed aib-typed" data-typing='@json($prompts)' data-caret="ai-caret"></span>
            </span>
        </button>
        @php
            // number, suffix, decimals, label
            $stats = [[80, '+', 0, 'Projects Delivered'], [40, '+', 0, 'Happy Clients'], [4.9, '★', 1, 'Client Rating'], [24, '/7', 0, 'Digital Support']];
        @endphp
        <div class="hero-stats" data-a="up" style="--d:.9s">
            @foreach($stats as [$value, $suffix, $decimals, $label])
                <div class="hero-stat">
                    <strong><span data-count="{{ $value }}" data-suffix="{{ $suffix }}" data-decimals="{{ $decimals }}">0</span></strong>
                    {{ $label }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="hero-rings" aria-hidden="true"><i></i></div>

    @php
        $orbit = [['01', 'Web Experience', 'slide3.webp'], ['02', 'Digital Product', 'slide1.webp'], ['03', 'UI / UX Design', 'slide2.webp'], ['04', 'Brand System', 'banner.webp'], ['05', 'Web Experience', 'slide3.webp'], ['06', 'Digital Product', 'slide1.webp']];
    @endphp
    <div class="orbit" aria-label="Creative outputs">
        <div class="orbit-track">
            @for($c = 0; $c < 2; $c++)
                @foreach($orbit as [$n, $label, $file])
                    <figure class="orbit-card" @if($c > 0) aria-hidden="true" @endif>
                        <img src="{{ asset("assets/img/" . $file) }}" alt="{{ $c === 0 ? $label : "" }}" width="320" height="220" @if($c > 0 || $n > 2) loading="lazy" @endif decoding="async" draggable="false">
                        <figcaption><b>{{ $n }}</b><span>{{ $label }}</span></figcaption>
                    </figure>
                @endforeach
            @endfor
        </div>
    </div>

</section>
