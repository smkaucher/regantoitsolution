@extends('layouts.app')
@section('title', 'About Us – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Learn about our senior studio building clean UI/UX and fast, scalable websites for startups and businesses worldwide.')
@section('content')
    @include('pages._head', ['title' => 'About Us', 'lead' => 'A small, senior studio building clean UI/UX and fast, scalable websites for startups and businesses worldwide.'])
    <section class="block pg-about">
        <div class="pg-split">
            <div>
                <h2>Design. Develop. Grow.</h2>
                <p>{{ $site['site_name'] ?? 'Reganto IT Solution' }} is a full-service digital studio. We combine thoughtful UX with premium UI and production-ready code, delivering clean systems that support long-term growth.</p>
                <p>From a first prototype to a polished platform, one team handles research, design, engineering and launch, so nothing gets lost between hand-offs.</p>
                <a class="btn" href="{{ url('/contact') }}">Start a project <x-icon name="arrow-right" /></a>
            </div>
            <ul class="pg-values">
                <li><b>Clarity first</b><span>Simple interfaces that convert.</span></li>
                <li><b>Built to last</b><span>Clean code, scalable foundations.</span></li>
                <li><b>Fast delivery</b><span>Short cycles, honest timelines.</span></li>
            </ul>
        </div>
    </section>
    @if($stats->count())
    <section class="block"><div class="pg-stats">
        @foreach($stats as $s)<div><b>{{ rtrim(rtrim(number_format($s->value, 1), '0'), '.') }}{{ $s->suffix }}</b><span>{{ $s->label }}</span></div>@endforeach
    </div></section>
    @endif
    @if($team->count())
    <section class="block"><h2>Meet the team</h2>@include('pages._team', ['team' => $team])
        <p class="center"><a class="btn btn-sm" href="{{ url('/team') }}">Full team</a></p></section>
    @endif
@endsection
