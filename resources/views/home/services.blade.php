@php $icons = ['uiux-design' => 'layers', 'development' => 'gear', 'branding' => 'sparkle', 'marketing' => 'chart', 'cyber-security' => 'shield']; @endphp
<section class="block svc2-sec" id="services">
    <div class="svc2-head" data-replay data-threshold="0.5">
        @php $w = 0; @endphp
        <span class="svc2-eyebrow" data-a="up">WHAT WE DO</span>
        <h2 aria-label="Software, UI/UX Design, Branding, Marketing &amp; Cyber Security Services">
            @foreach(['Software, UI/UX Design, Branding,', 'Marketing & Cyber Security Services'] as $line)
                <span class="svc2-line" aria-hidden="true">
                    @foreach(explode(' ', $line) as $word)
                        <span class="why-word" data-a="word" style="--d:{{ $w++ * 0.05 }}s">{{ $word }}</span>
                    @endforeach
                </span>
            @endforeach
        </h2>
    </div>
    <div class="svc2" data-tabs>
        <div class="svc2-tabs" role="tablist">
            @foreach($services as $i => $s)
                <button type="button" role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" class="{{ $i === 0 ? 'on' : '' }}" data-tab="{{ $i }}">
                    <span class="svc2-ind"></span>
                    <span class="svc2-ico"><x-icon :name="$icons[$s->slug] ?? 'rocket'" size="18" /></span>
                    <b>{{ $s->title }}</b><small>{{ $s->items->count() }}</small>
                </button>
            @endforeach
        </div>
        <div class="svc2-body" role="tabpanel">
            @foreach($services as $i => $s)
                <div class="svc2-grid" data-panel="{{ $i }}" @if($i !== 0) hidden @endif>
                    @foreach($s->items as $j => $item)
                        <article style="--d:{{ $j * 0.07 }}s">
                            <span>{{ str_pad($j + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <h4>{{ $item->title }}</h4>
                            <p>{{ $item->description }}</p>
                        </article>
                    @endforeach
                </div>
            @endforeach
            <p class="svc2-cta"><a class="btn btn-sm" href="{{ url('/services') }}">Explore all services <x-icon name="arrow-circle" size="16" /></a></p>
        </div>
    </div>
</section>
