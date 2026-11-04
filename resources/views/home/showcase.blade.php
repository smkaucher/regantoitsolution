@php
    $cards = [['01', 'Web Experience', 'slide3.png'], ['02', 'Digital Product', 'slide1.jpg'], ['03', 'UI / UX Design', 'slide2.jpg'], ['04', 'Brand System', 'banner.jpg'], ['05', 'Web Experience', 'slide3.png'], ['06', 'Digital Product', 'slide1.jpg']];
    $prompts = ['Design a product dashboard for my SaaS', 'Build an e-commerce website that sells', 'Create a brand identity for my startup', 'Redesign my mobile app UI/UX'];
@endphp
<section class="block cx" data-replay data-threshold="0.25" id="showcase">
    <h2 class="cx-title">01 Creative Outputs</h2>
    <div class="cx-stage">
        <div class="cx-track">
            @for($c = 0; $c < 4; $c++)
                @foreach($cards as [$n, $label, $file])
                    <figure class="cx-card" @if($c > 0) aria-hidden="true" @endif>
                        <img src="{{ asset('assets/img/' . $file) }}" alt="{{ $c === 0 ? $label : '' }}" loading="lazy" draggable="false">
                        <figcaption><b>{{ $n }}</b><span>{{ $label }}</span></figcaption>
                    </figure>
                @endforeach
            @endfor
        </div>
    </div>
    <div class="cx-prompt">
        <span class="cx-spark"><x-icon name="sparkle" size="22" /></span>
        <div class="cx-ptext">
            <small>REGANTO AI CREATIVE ASSIST</small>
            <span class="cx-typed" data-typing='@json($prompts)' data-caret="cx-caret"></span>
        </div>
        <a class="cx-go" href="{{ url('/contact') }}" aria-label="Start your project">↗</a>
    </div>
    <button type="button" class="cx-scroll" data-scroll-next>
        <span class="cx-mouse"><i></i></span>
        SCROLL TO DISCOVER
    </button>
</section>
