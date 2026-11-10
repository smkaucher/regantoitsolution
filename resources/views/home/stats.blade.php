@php
    $verified = 'Verified <svg class="fb-verified" viewBox="0 0 24 24" width="18" height="18" role="img" aria-label="Verified on Facebook"><circle cx="12" cy="12" r="10.5" fill="#1877F2"/><path d="M7.6 12.4l3 3 5.8-6.1" fill="none" stroke="#fff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $badges = [['4.9', 'Rating · Clutch Reviewed'], ['USA · Dubai', 'Based in'], ['10M', 'Views · Behance Most View'], ['10M', 'Views · Dribbble Most View'], [$verified, 'Facebook']];
    $name = $site['site_name'] ?? 'Reganto IT Solution';
@endphp
<section class="abt" id="about" data-replay data-threshold="0.25">
    <div class="abt-glow g1" aria-hidden="true"></div>
    <div class="abt-glow g2" aria-hidden="true"></div>

    <div class="abt-head">
        <span class="abt-eyebrow" data-a="up">ABOUT US</span>
        <h2 data-a="up" style="--d:.1s">A USA-Based UI/UX &amp; Web Team for <em>Global Brands</em></h2>
    </div>

    <div class="abt-stats">
        @foreach($stats as $i => $s)
            <div class="abt-stat" data-a="up" style="--d:{{ .3 + $i * .1 }}s">
                <strong><span data-count="{{ (float) $s->value }}" data-suffix="{{ $s->suffix }}" data-decimals="{{ floor($s->value) == $s->value ? 0 : 1 }}">0</span></strong>
                <span>{{ $s->label }}</span>
            </div>
        @endforeach
    </div>

    <div class="abt-proof" data-a="up" style="--d:.55s">
        <h3>{{ strtoupper($name) }}’S 3 Years of Innovation</h3>
        <div class="abt-badges">
            @foreach($badges as [$n, $l])
                <div><strong>{!! $n !!}</strong><small>{{ $l }}</small></div>
            @endforeach
        </div>
        <p class="abt-chips">
            @foreach(['24/7 SUPPORT', 'WEEKLY UPDATES', 'SECURE & COMPLIANT', '99.9% UPTIME'] as $c)
                <span><x-icon name="check" size="12" /> {{ $c }}</span>
            @endforeach
        </p>
        <a class="btn" href="{{ $site['book_call_url'] ?? url('/contact') }}">Book a FREE Strategy Call <x-icon name="arrow-circle" size="16" /></a>
    </div>
</section>
