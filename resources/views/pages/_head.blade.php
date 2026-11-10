<section class="hero pg-hero" data-replay data-threshold="0.25">
    <div class="hero-glow g1" aria-hidden="true"></div>
    <div class="hero-glow g2" aria-hidden="true"></div>
    <div class="hero-rings" aria-hidden="true"><i></i></div>

    <div class="hero-copy">
        <small class="pg-crumb" data-a="up" style="--d:.05s"><a href="{{ url('/') }}">Home</a> / {{ $crumb ?? $title }}</small>
        <h1>
            <span class="hl"><span data-a="line" style="--d:.17s">{{ $title }}</span></span>
        </h1>
        @isset($lead)<p data-a="up" style="--d:.35s">{{ $lead }}</p>@endisset
    </div>
</section>
