@extends('layouts.app')
@section('title', 'Projects – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Recent UI/UX, web, software, graphics and branding work focused on speed, clarity and conversion.')
@section('content')
    @include('pages._head', ['title' => 'Our Projects', 'crumb' => 'Projects', 'lead' => 'Recent UI/UX, web, software, graphics and branding work. Click any project to see the details.'])
    <section class="block" id="pgFilter">
        <div class="tabs">
            <button type="button" class="on" data-cat="All">All</button>
            @foreach($tabs as $t)<button type="button" data-cat="{{ $t }}">{{ $t }}</button>@endforeach
        </div>
        <div class="pg-cards pg-proj">
            @forelse($projects as $p)
                @php $cover = \App\Support\Media::url($p->cover_image); @endphp
                <a class="pg-card" data-slug="{{ $p->slug }}" data-tags='@json($p->tags())' href="{{ url('/projects/' . $p->slug) }}">
                    @if($cover)<img src="{{ $cover }}" alt="{{ $p->title }}" loading="lazy">@else<div class="pg-ph">{{ $p->title }}</div>@endif
                    <small>{{ $p->category }}</small><h3>{{ $p->title }}</h3>
                </a>
            @empty<p class="sub">No projects yet.</p>@endforelse
        </div>
    </section>

    <div class="pm" id="pm" hidden>
        <div class="pm-back" data-close></div>
        <div class="pm-box" role="dialog" aria-modal="true" aria-labelledby="pmTitle">
            <button type="button" class="pm-x" data-close aria-label="Close">×</button>
            <div class="pm-scroll">
                <div class="pm-top">
                    <div class="pm-gal"><img id="pmMain" alt=""><div class="pm-thumbs" id="pmThumbs"></div></div>
                    <div class="pm-info">
                        <small id="pmCat"></small>
                        <h2 id="pmTitle"></h2>
                        <p class="pm-sum" id="pmSum"></p>
                        <div class="pm-desc" id="pmDesc"></div>
                        <ul class="pm-meta" id="pmMeta"></ul>
                        <a class="btn btn-sm" id="pmVisit" target="_blank" rel="noreferrer">Visit site ↗</a>
                    </div>
                </div>
                <div class="pm-more">
                    <h3>More work</h3>
                    <div class="pm-more-grid" id="pmMore"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
(function () {
    var DATA = @json($data);
    var bySlug = {}; DATA.forEach(function (d) { bySlug[d.slug] = d; });
    var wrap = document.getElementById('pgFilter');
    var tabs = wrap.querySelectorAll('.tabs button');
    var cards = wrap.querySelectorAll('.pg-card');
    var modal = document.getElementById('pm');
    var $ = function (id) { return document.getElementById(id); };
    var current = null;

    tabs.forEach(function (b) {
        b.addEventListener('click', function () {
            tabs.forEach(function (x) { x.classList.toggle('on', x === b); });
            cards.forEach(function (c) {
                var tags = JSON.parse(c.dataset.tags || '[]');
                c.hidden = b.dataset.cat !== 'All' && tags.indexOf(b.dataset.cat) === -1;
            });
        });
    });

    function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }

    function setImage(src) {
        $('pmMain').src = src;
        $('pmThumbs').querySelectorAll('button').forEach(function (t) { t.classList.toggle('on', t.dataset.src === src); });
    }

    function render(d) {
        current = d;
        $('pmCat').textContent = d.category || '';
        $('pmTitle').textContent = d.title;
        $('pmSum').textContent = d.summary || '';
        $('pmDesc').innerHTML = d.description || '';
        var meta = '';
        if (d.client) meta += '<li><b>Client</b><span>' + esc(d.client) + '</span></li>';
        if (d.category) meta += '<li><b>Category</b><span>' + esc(d.category) + '</span></li>';
        $('pmMeta').innerHTML = meta;
        var v = $('pmVisit'); v.hidden = !d.url; if (d.url) v.href = d.url;

        $('pmThumbs').innerHTML = d.images.length > 1 ? d.images.map(function (s) {
            return '<button type="button" data-src="' + esc(s) + '"><img src="' + esc(s) + '" alt="" loading="lazy"></button>';
        }).join('') : '';
        $('pmThumbs').querySelectorAll('button').forEach(function (t) { t.addEventListener('click', function () { setImage(t.dataset.src); }); });
        $('pmMain').alt = d.title;
        setImage(d.images[0] || '');

        var others = DATA.filter(function (x) { return x.slug !== d.slug; });
        others.sort(function (a, b) {
            var sa = a.tags.some(function (t) { return d.tags.indexOf(t) > -1; }) ? 0 : 1;
            var sb = b.tags.some(function (t) { return d.tags.indexOf(t) > -1; }) ? 0 : 1;
            return sa - sb;
        });
        $('pmMore').innerHTML = others.slice(0, 6).map(function (o) {
            return '<a href="/projects/' + esc(o.slug) + '" data-open="' + esc(o.slug) + '">' +
                (o.images[0] ? '<img src="' + esc(o.images[0]) + '" alt="" loading="lazy">' : '<span class="pg-ph">' + esc(o.title) + '</span>') +
                '<b>' + esc(o.title) + '</b></a>';
        }).join('');
        $('pmMore').querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function (e) { e.preventDefault(); render(bySlug[a.dataset.open]); modal.querySelector('.pm-scroll').scrollTop = 0; });
        });
    }

    function open(slug) {
        if (!bySlug[slug]) return;
        render(bySlug[slug]);
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        modal.querySelector('.pm-scroll').scrollTop = 0;
        requestAnimationFrame(function () { modal.classList.add('on'); });
    }
    function close() {
        modal.classList.remove('on');
        document.body.style.overflow = '';
        setTimeout(function () { modal.hidden = true; }, 200);
    }

    cards.forEach(function (c) {
        c.addEventListener('click', function (e) { e.preventDefault(); open(c.dataset.slug); });
    });
    modal.querySelectorAll('[data-close]').forEach(function (x) { x.addEventListener('click', close); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) close(); });

    var want = new URLSearchParams(location.search).get('open');
    if (want) open(want);
})();
</script>
@endpush
