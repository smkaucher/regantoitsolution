@php
    $motion = [[3.4, 0], [4.2, .5], [3.8, 1], [4.6, .3]];
@endphp
<section class="stats" id="about" data-replay data-threshold="0.3">
    <div class="stats-copy">
        <h2 data-a="left" style="--d:.21s">A USA-Based UI/UX &amp; Web Team for Global Brands <b class="underline" data-a="grow" style="--d:.7s"></b></h2>
        <p data-a="up" style="--d:.37s">{{ $site['site_name'] ?? 'Reganto IT Solution' }} is a USA-based digital studio delivering premium UI/UX, development, and branding. Our process is strategy-first — research, clean design, and scalable code to help clients grow with confidence.</p>
        <a class="btn btn-sm" href="{{ $site['book_call_url'] ?? url('/contact') }}" data-a="up" style="--d:.53s">Book a FREE Strategy Call <x-icon name="arrow-circle" size="16" /></a>
    </div>
    <div class="stat-grid">
        @foreach($stats as $i => $s)
            <div class="stat s{{ $i + 1 }}" style="--fd:{{ $motion[$i][0] }}s;--fdl:{{ $motion[$i][1] }}s">
                <strong><span data-count="{{ (float) $s->value }}" data-suffix="{{ $s->suffix }}" data-decimals="{{ floor($s->value) == $s->value ? 0 : 1 }}">0</span></strong>
                <span>{{ $s->label }}</span>
            </div>
        @endforeach
    </div>
</section>
