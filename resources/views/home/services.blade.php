{{--
    Home "Our Services": a pinned, scroll-driven section, one step per service category from the top bar.
    While the visitor scrolls through .svs-track, .svs-pin stays on screen. On the right, the categories are layers
    of an isometric stack (pure CSS 3D): the active layer slides out and turns teal, and the gaps open up as the
    section starts. On the left, the panel of the active category fades in. home.js (initServiceScroll) turns the
    scroll position into --pp (0..1) and the active step; on phones every panel is listed (no pinning).
    The Services page keeps the tab browser (partials/services-browser).
--}}
@php
    $n = $services->count();
    $pad = fn ($i) => str_pad($i, 2, '0', STR_PAD_LEFT);
@endphp
<section class="svs" data-svs style="--n:{{ $n }};--pp:0">
    <div class="svs-track">
        <div class="svs-pin">
            <div class="svs-head wk2-head">
                <span class="svc2-eyebrow">OUR SERVICES</span>
                <h2>Everything your business <em>needs.</em></h2>
            </div>
            <div class="svs-left">
                {{-- /home2 only: the travelling laptop, folded into a monitor, docks here and shows the panels below --}}
                @if(!empty($monitorSlot))<div class="svs-mslot" data-dv-slot="services" aria-hidden="true"></div>@endif

                <div class="svs-panels">
                    @foreach($services as $i => $s)
                        <article class="svs-panel {{ $i === 0 ? 'on' : '' }}" data-svs-panel>
                            <p class="svs-count"><b>{{ $pad($i + 1) }}</b> / {{ $pad($n) }}</p>
                            <h3 class="svs-name">{{ $s->title }}</h3>
                            <p class="svs-short">{{ $s->short_description }}</p>
                            <p class="svs-out"><b>{{ $s->items_count }}+</b><span>services in this category</span></p>
                            <ul class="svs-tools">
                                @foreach($s->items->take(4) as $item)
                                    <li>{{ $item->title }}</li>
                                @endforeach
                            </ul>
                            <a class="svs-cta" href="{{ url('/services/' . $s->slug) }}">Explore the service <span><x-icon name="arrow-right" size="14" /></span></a>
                        </article>
                    @endforeach
                </div>

                <div class="svs-index" role="tablist" aria-label="Service categories">
                    @foreach($services as $i => $s)
                        <button type="button" class="{{ $i === 0 ? 'on' : '' }}" data-svs-step="{{ $i }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"><span>{{ $pad($i + 1) }}</span> {{ $s->title }}</button>
                    @endforeach
                </div>
            </div>

            <div class="svs-scene" aria-hidden="true">
                {{-- The Reganto mark, redrawn as strokes (ring arcs, the R, the grey leg), drawn in and turned by --pp --}}
                <svg class="svs-logo" viewBox="0 0 100 100" fill="none" stroke-width="7" stroke-linecap="round" stroke-linejoin="round">
                    <path class="t" pathLength="100" d="M15.2 26.5 A42 42 0 1 1 79.2 80.2" />
                    <path class="t" pathLength="100" d="M7 35.4 H64 A9 9 0 0 1 64 53.4 H39 L80 81" />
                    <path class="g" pathLength="100" d="M8.4 44.2 A42 42 0 0 0 58.7 91.1" />
                    <path class="g" pathLength="100" d="M20 52 L66 85" />
                </svg>
                <div class="svs-iso">
                    @foreach($services as $i => $s)
                        <div class="svs-slab {{ $i === 0 ? 'on' : '' }}" style="--i:{{ $i }};--z:{{ $n - 1 - $i }}" data-svs-slab>
                            <div class="svs-top">
                                <span class="svs-grid"></span>
                                <span class="svs-tn">{{ $pad($i + 1) }}</span>
                                <span class="svs-tt">{{ $s->title }}</span>
                            </div>
                            <div class="svs-sf"></div>
                            <div class="svs-sr"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
