@extends('layouts.app')
@section('title', $service->title . ' – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', $service->short_description)
@section('og_image', \App\Support\Media::url($service->image) ?? '')
@section('content')
    @include('pages._head', ['title' => $service->title, 'crumb' => 'Services / ' . $service->title, 'lead' => $service->short_description])
    <section class="block pg-split svc-page">
        <div>
            @if($service->image)<img class="pg-cover" src="{{ \App\Support\Media::url($service->image) }}" alt="{{ $service->title }}">@endif
            <div class="pg-prose">{!! $service->details ?: '<p>' . e($service->short_description) . '</p>' !!}</div>

            @if($service->items->count())
                <h3 class="svc-h">What we deliver</h3>
                <div class="svc-grid" data-replay data-threshold="0.1">
                    @foreach($service->items as $n => $i)
                        <article class="svc-item" data-a="up" style="--d:{{ min($n, 6) * .06 }}s">
                            <span class="svc-num">{{ str_pad($n + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <b>{{ $i->title }}</b>
                            @if($i->description)<p>{{ $i->description }}</p>@endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
        <aside class="svc-book">
            <h3>Book this service</h3>
            <p>Message or call us and we will get back within 30 minutes.</p>
            @include('partials.contact-actions', ['service' => $service])
        </aside>
    </section>
@endsection
