@extends('layouts.app')
@section('title', 'Projects – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Recent UI/UX and development projects focused on speed, clarity and conversion.')
@section('content')
    @include('pages._head', ['title' => 'Our Projects', 'crumb' => 'Projects', 'lead' => 'Recent UI/UX and development work focused on speed, clarity and conversion.'])
    <section class="block" id="pgFilter">
        <div class="tabs">
            <button type="button" class="on" data-cat="All">All</button>
            @foreach($categories as $c)<button type="button" data-cat="{{ $c }}">{{ $c }}</button>@endforeach
        </div>
        <div class="pg-cards pg-proj">
            @forelse($projects as $p)
                <a class="pg-card" data-cat="{{ $p->category }}" href="{{ url('/projects/' . $p->slug) }}">
                    @if($p->cover_image)<img src="{{ \App\Support\Media::url($p->cover_image) }}" alt="{{ $p->title }}" loading="lazy">@else<div class="pg-ph">{{ $p->title }}</div>@endif
                    <small>{{ $p->category }}</small><h3>{{ $p->title }}</h3><p>{{ $p->short_description }}</p>
                </a>
            @empty<p class="sub">No projects yet.</p>@endforelse
        </div>
    </section>
@endsection
@push('scripts')
<script>
(function () {
    var w = document.getElementById('pgFilter');
    var tabs = w.querySelectorAll('.tabs button');
    tabs.forEach(function (b) {
        b.addEventListener('click', function () {
            tabs.forEach(function (x) { x.classList.toggle('on', x === b); });
            w.querySelectorAll('.pg-card').forEach(function (c) {
                c.hidden = b.dataset.cat !== 'All' && c.dataset.cat !== b.dataset.cat;
            });
        });
    });
})();
</script>
@endpush
