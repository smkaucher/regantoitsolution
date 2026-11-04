@php
    $bgs = ['linear-gradient(135deg,#5a9cf5,#3a6fd8)', 'linear-gradient(135deg,#2e8b7a,#74d4c2)', 'linear-gradient(135deg,#9b6bd1,#5b3aa8)', 'linear-gradient(135deg,#e08a4a,#b4552a)'];
@endphp
<section class="block wk" id="work" data-replay data-threshold="0.2" data-work>
    <div data-replay data-threshold="0.6">
        <h2 data-words data-stagger="0.05">Recent Work That Drives Results</h2>
        <p class="sub" data-words data-stagger="0.03">Our latest UI/UX and development projects — from SaaS dashboards to e-commerce experiences, focused on speed, clarity, and conversion.</p>
        <div class="tabs">
            <button type="button" class="on" data-cat="All" data-a="up" style="--d:0s">All</button>
            @foreach($workCategories as $i => $cat)
                <button type="button" data-cat="{{ $cat }}" data-a="up" style="--d:{{ ($i + 1) * 0.05 }}s">{{ $cat }}</button>
            @endforeach
        </div>
    </div>
    <p class="sub" data-empty hidden></p>
    <div class="wk-grid">
        @foreach($projects as $i => $p)
            @php
                $host = preg_replace('/^www\./', '', parse_url($p->url ?? '', PHP_URL_HOST) ?: '');
                $cover = \App\Support\Media::url($p->cover_image);
            @endphp
            <a href="{{ $p->url ?: url('/projects/' . $p->slug) }}" class="wk-card" data-cat="{{ $p->category }}" style="--i:{{ $i }}" target="_blank" rel="noreferrer">
                <div class="wk-shot" style="background:{{ $bgs[$i % 4] }}">
                    @if($cover)
                        <img src="{{ $cover }}" alt="{{ $p->title }} preview" loading="lazy" draggable="false">
                    @elseif($p->url)
                        <img data-shot="{{ $p->url }}" alt="{{ $p->title }} preview" loading="lazy" draggable="false">
                    @endif
                    <div class="wk-fallback" @if($cover) hidden @endif><b>{{ explode(' ', $p->title)[0] }}</b><small>{{ $host }}</small></div>
                    <span class="wk-visit">Visit site ↗</span>
                </div>
                <small>{{ $p->category }}{{ $host ? ' · ' . $host : '' }}</small>
                <h3>{{ $p->title }}</h3>
            </a>
        @endforeach
    </div>
    <p class="center"><a class="btn btn-sm" href="{{ url('/projects') }}">View More</a></p>
</section>
