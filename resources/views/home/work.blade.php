@php
    // tab label => project tag it filters on
    $workTabs = ['All' => 'All', 'Web Development' => 'Web Development', 'Software' => 'Software', 'UI/UX Design' => 'UI/UX Design', 'Branding' => 'Graphics & Branding'];
    // Card order per tab (slugs). Anything not listed keeps its normal order after these.
    $workOrder = [
        'All' => ['regantify-platform-landing', 'rareman-fashion-store', 'every-step-breathes', 'shopnet-multi-category-store', 'shopnet-mobile-home-ui', 'storepal-e-commerce-store', 'made-to-conquer-rain', 'rareman-mobile-ui', 'shopnet-ai-chat-ui', 'walk-with-authority', 'shopnet-mobile-categories-ui'],
        'Web Development' => ['regantify-platform-landing', 'rareman-fashion-store', 'shopnet-multi-category-store', 'storepal-e-commerce-store'],
        'Software' => ['storepal-e-commerce-store', 'regantify-platform-landing', 'shopnet-multi-category-store', 'rareman-fashion-store'],
        'UI/UX Design' => ['shopnet-ai-chat-ui', 'storepal-e-commerce-store', 'rareman-mobile-ui', 'regantify-platform-landing', 'shopnet-mobile-categories-ui', 'rareman-fashion-store', 'shopnet-mobile-home-ui', 'shopnet-multi-category-store'],
    ];
@endphp
<section class="block wk2" id="work" data-wk2>
    <div class="wk2-head" data-replay data-threshold="0.4">
        <span class="svc2-eyebrow" data-a="up">OUR WORK</span>
        <h2 data-a="up" style="--d:.1s">Recent Work That Drives Results</h2>
    </div>

    <div class="wk2-tabs" role="tablist">
        @foreach($workTabs as $label => $tag)
            <button type="button" role="tab" class="{{ $loop->first ? 'on' : '' }}" data-tag="{{ $tag }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $label }}</button>
        @endforeach
    </div>

    <div class="wk2-grid" id="wk2Grid">
        @foreach($projects as $p)
            @php
                $cover = \App\Support\Media::url($p->cover_image);
                $portrait = in_array($p->category, ['Graphics Design'], true);
            @endphp
            @continue(!$cover)
            <a href="{{ url('/projects?open=' . $p->slug) }}" class="wk2-card {{ $portrait ? 'is-portrait' : '' }}" data-slug="{{ $p->slug }}" data-i="{{ $loop->index }}" data-tags='@json($p->tags())' hidden>
                <span class="wk2-shot"><img src="{{ $cover }}" alt="{{ $p->title }}" loading="lazy" draggable="false"></span>
                <span class="wk2-cap">
                    <small>{{ $p->category }}</small>
                    <b>{{ $p->title }}</b>
                </span>
                <span class="wk2-go"><x-icon name="arrow-right" size="16" /></span>
            </a>
        @endforeach
    </div>
    <p class="wk2-more"><a class="btn btn-sm" href="{{ url('/projects') }}">View all projects <x-icon name="arrow-circle" size="16" /></a></p>
</section>
<script>
(function () {
    var root = document.querySelector('[data-wk2]');
    if (!root) return;
    var tabs = root.querySelectorAll('.wk2-tabs button');
    var cards = root.querySelectorAll('.wk2-card');
    var ORDER = @json($workOrder);
    var GROW = [2, 1, 1.4, 1.6, 1, 2, 1.5, 1.1]; // varied widths; every row still fills the full width
    function show(tag) {
        var grid = document.getElementById('wk2Grid');
        grid.classList.toggle('is-all', tag === 'All');
        grid.classList.toggle('is-brand', tag === 'Graphics & Branding');
        var list = ORDER[tag] || [];
        var n = 0;
        cards.forEach(function (c) {
            var ok = tag === 'All' || JSON.parse(c.dataset.tags || '[]').indexOf(tag) > -1;
            c.hidden = !ok;
            if (!ok) return;
            var r = list.indexOf(c.dataset.slug);
            c.style.order = r > -1 ? r : 100 + parseInt(c.dataset.i, 10);
            c.style.setProperty('--g', GROW[n % GROW.length]);
            c.style.setProperty('--n', Math.min(n++, 10));
            c.style.animation = 'none'; void c.offsetWidth; c.style.animation = '';
        });
    }
    tabs.forEach(function (b) {
        b.addEventListener('click', function () {
            tabs.forEach(function (x) { x.classList.toggle('on', x === b); x.setAttribute('aria-selected', x === b ? 'true' : 'false'); });
            show(b.dataset.tag);
        });
    });
    show(tabs[0].dataset.tag);
})();
</script>
