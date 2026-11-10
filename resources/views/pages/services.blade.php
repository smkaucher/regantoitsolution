@extends('layouts.app')
@section('title', 'Services – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Software, UI/UX design, branding, marketing and cyber security services, from first sketch to production.')
@section('content')
    @include('pages._head', ['title' => 'Our Services', 'crumb' => 'Services', 'lead' => 'Software, UI/UX design, branding, marketing and cyber security, from first sketch to production.'])
    <section class="block"><div class="pg-cards">
        @foreach($services as $s)
            <a class="pg-card" href="{{ url('/services/' . $s->slug) }}">
                <h3>{{ $s->title }}</h3>
                <p>{{ $s->short_description }}</p>
                <ul>@foreach($s->items->take(5) as $i)<li>{{ $i->title }}</li>@endforeach</ul>
                <span>Learn more →</span>
            </a>
        @endforeach
    </div></section>
@endsection
