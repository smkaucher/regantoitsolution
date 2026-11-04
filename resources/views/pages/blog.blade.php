@extends('layouts.app')
@section('title', $blog->title . ' – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', $blog->excerpt)
@section('og_image', \App\Support\Media::url($blog->image) ?? '')
@section('og_type', 'article')
@section('content')
    @include('pages._head', ['title' => $blog->title, 'crumb' => 'Blog', 'lead' => trim(optional($blog->published_at)->format('M j, Y') . ($blog->category ? ' · ' . $blog->category : ''), ' ·')])
    <article class="block pg-article">
        @if($blog->image)<img class="pg-cover" src="{{ \App\Support\Media::url($blog->image) }}" alt="">@endif
        <div class="pg-prose">{!! $blog->content ?: '<p>' . e($blog->excerpt) . '</p>' !!}</div>
    </article>
    @if($more->count())
    <section class="block"><h2>Keep reading</h2><div class="posts">
        @foreach($more as $b)
            <a href="{{ url('/blog/' . $b->slug) }}">@if($b->image)<img src="{{ \App\Support\Media::url($b->image) }}" alt="">@endif<small>{{ optional($b->published_at)->format('M j, Y') }}</small><h3>{{ $b->title }}</h3></a>
        @endforeach
    </div></section>
    @endif
@endsection
