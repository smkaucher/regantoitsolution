@extends('layouts.app')
@section('title', (($open->title ?? null) ? $open->title . ' · ' : '') . $service->title . ' – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', $open->description ?? $service->short_description)
@section('og_image', \App\Support\Media::url($service->image) ?? '')
@section('content')
    @include('pages._head', ['title' => $service->title, 'crumb' => 'Services / ' . $service->title, 'lead' => $service->short_description])
    <section class="block pg-split svc-page">
        <div>
            @if($service->image)<img class="pg-cover" src="{{ \App\Support\Media::url($service->image) }}" alt="{{ $service->title }}" loading="lazy">@endif

            @if($service->items->count())
                <ul class="sc-grid">
                    @foreach($service->items as $i)
                        @php $cover = \App\Support\Media::url($i->image); @endphp
                        <li>
                            <a class="sc-card" data-svc-pop href="{{ url('/services/' . $service->slug . '/' . $i->slug) }}">
                                <span class="sc-shot {{ $cover ? '' : 'is-plain' }}">
                                    @if($cover)<img src="{{ $cover }}" alt="" loading="lazy" width="960" height="600" decoding="async">@else<x-icon :name="$service->icon ?: 'rocket'" size="34" />@endif
                                </span>
                                <span class="sc-text">
                                    <b>{{ $i->title }}</b>
                                    <small>{{ count($i->points ?? []) ?: '' }}{{ count($i->points ?? []) ? ' things included' : '' }}</small>
                                </span>
                                <span class="sc-go"><x-icon name="arrow-right" size="16" /></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <aside class="svc-book">
            <h3>Talk to us about {{ $service->title }}</h3>
            <p>Message or call us and we will get back within 30 minutes.</p>
            @include('partials.contact-actions', ['service' => $service])
            @if(($others ?? collect())->count())
                <h4 class="sv-other-h">Other services</h4>
                <div class="svc-others">
                    @foreach($others as $o)
                        <a href="{{ url('/services/' . $o->slug) }}">{{ $o->title }} <x-icon name="arrow-right" size="14" /></a>
                    @endforeach
                </div>
            @endif
        </aside>
    </section>

    @php
        $workCards = $work->filter(fn ($p) => \App\Support\Media::url($p->cover_image))->values();
        $grow = [2, 1, 1.4, 1.6, 1, 2, 1.5, 1.1]; // varied widths, every row still fills the full width
        $tall = $workCards->isNotEmpty() && $workCards->every(fn ($p) => in_array($p->category, ['Graphics Design', 'Branding'], true));
    @endphp
    @if($workCards->isNotEmpty())
        <section class="block wk2 sw">
            <div class="wk2-head">
                <h2>Recent Work in {{ $service->title }}</h2>
            </div>
            <div class="wk2-grid {{ $tall ? 'is-brand' : '' }}">
                @foreach($workCards as $n => $p)
                    <a href="{{ url('/projects?open=' . $p->slug) }}" class="wk2-card {{ in_array($p->category, ['Graphics Design'], true) ? 'is-portrait' : '' }}" style="--g:{{ $grow[$n % count($grow)] }};--n:{{ min($n, 10) }}">
                        <span class="wk2-shot"><img src="{{ \App\Support\Media::url($p->cover_image) }}" alt="{{ $p->title }}" loading="lazy" draggable="false"></span>
                        <span class="wk2-cap">
                            <small>{{ $p->category }}</small>
                            <b>{{ $p->title }}</b>
                        </span>
                        <span class="wk2-go"><x-icon name="arrow-right" size="16" /></span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
    @if($open)
        {{-- Opened from a direct link: the script shows this service in the pop-up. --}}
        <span hidden data-svc-open="{{ url('/services/' . $service->slug . '/' . $open->slug) }}" data-svc-base="{{ url('/services/' . $service->slug) }}"></span>
    @endif
@endsection
