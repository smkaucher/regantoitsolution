@extends('layouts.app')
@section('title', $service->title . ' – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', $service->short_description)
@section('og_image', \App\Support\Media::url($service->image) ?? '')
@section('content')
    @include('pages._head', ['title' => $service->title, 'crumb' => 'Services / ' . $service->title, 'lead' => $service->short_description])
    <section class="block pg-split">
        <div>
            @if($service->image)<img class="pg-cover" src="{{ \App\Support\Media::url($service->image) }}" alt="{{ $service->title }}">@endif
            <div class="pg-prose">{!! $service->details ?: '<p>' . e($service->short_description) . '</p>' !!}</div>
            <a class="btn" href="{{ url('/contact') }}?service={{ $service->id }}">Request this service <x-icon name="arrow-right" /></a>
        </div>
        <aside class="pg-side">
            <h4>What we deliver</h4>
            <ul>@foreach($service->items as $i)<li><b>{{ $i->title }}</b>@if($i->description)<span>{{ $i->description }}</span>@endif</li>@endforeach</ul>
            <h4>Other services</h4>
            @foreach($others as $o)<a href="{{ url('/services/' . $o->slug) }}">{{ $o->title }}</a>@endforeach
        </aside>
    </section>
@endsection
