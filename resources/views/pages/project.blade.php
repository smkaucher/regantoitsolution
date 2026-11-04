@extends('layouts.app')
@section('title', $project->title . ' – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', $project->short_description)
@section('og_image', \App\Support\Media::url($project->cover_image) ?? '')
@section('content')
    @include('pages._head', ['title' => $project->title, 'crumb' => 'Projects / ' . $project->title, 'lead' => $project->short_description])
    <section class="block pg-split">
        <div>
            @if($project->cover_image)<img class="pg-cover" src="{{ \App\Support\Media::url($project->cover_image) }}" alt="{{ $project->title }}">@endif
            <div class="pg-prose">{!! $project->description ?: '<p>' . e($project->short_description) . '</p>' !!}</div>
            @if($project->images->count())<div class="pg-gallery">@foreach($project->images as $img)<img src="{{ \App\Support\Media::url($img->path) }}" alt="" loading="lazy">@endforeach</div>@endif
        </div>
        <aside class="pg-side">
            <h4>Project info</h4>
            <ul>
                @if($project->client)<li><b>Client</b><span>{{ $project->client }}</span></li>@endif
                @if($project->category)<li><b>Category</b><span>{{ $project->category }}</span></li>@endif
            </ul>
            @if($project->url)<a class="btn btn-sm" href="{{ $project->url }}" target="_blank" rel="noreferrer">Visit site ↗</a>@endif
        </aside>
    </section>
    @if($related->count())
    <section class="block"><h2>More projects</h2><div class="pg-cards pg-proj">
        @foreach($related as $p)
            <a class="pg-card" href="{{ url('/projects/' . $p->slug) }}">
                @if($p->cover_image)<img src="{{ \App\Support\Media::url($p->cover_image) }}" alt="">@else<div class="pg-ph">{{ $p->title }}</div>@endif
                <small>{{ $p->category }}</small><h3>{{ $p->title }}</h3>
            </a>
        @endforeach
    </div></section>
    @endif
@endsection
