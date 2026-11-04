@extends('layouts.app')
@section('title', 'Blog – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Guides, trends and lessons on UI/UX, design and development from our team.')
@section('content')
    @include('pages._head', ['title' => 'Insights & Guides', 'crumb' => 'Blog', 'lead' => 'Guides, trends and lessons from the team.'])
    <section class="block"><div class="posts">
        @forelse($blogs as $b)
            <a href="{{ url('/blog/' . $b->slug) }}">
                @if($b->image)<img src="{{ \App\Support\Media::url($b->image) }}" alt="" loading="lazy">@endif
                <small>{{ optional($b->published_at)->format('M j, Y') }}{{ $b->category ? ' · ' . $b->category : '' }}</small>
                <h3>{{ $b->title }}</h3><p class="pg-ex">{{ $b->excerpt }}</p>
            </a>
        @empty<p class="sub">No articles yet.</p>@endforelse
    </div></section>
@endsection
