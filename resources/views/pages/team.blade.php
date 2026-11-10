@extends('layouts.app')
@section('title', 'Our Team – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Meet the designers and engineers behind every project.')
@section('content')
    @include('pages._head', ['title' => 'Our IT Team', 'crumb' => 'Team', 'lead' => 'The designers and engineers behind every project.'])
    @forelse($groups as $g)
        <section class="block tm-group">
            @if($g['name'])<h2>{{ $g['name'] }}</h2>@endif
            @include('pages._team', ['team' => $g['members'], 'cover' => $g['cover'] ?? null])
        </section>
    @empty
        <section class="block"><p class="sub">Team members will appear here soon.</p></section>
    @endforelse
    <script>
    (function(){var g=document.querySelectorAll('.tm-group');
    if(!('IntersectionObserver' in window)){g.forEach(function(e){e.classList.add('in')});return}
    var o=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');o.unobserve(e.target)}})},{threshold:.15});
    g.forEach(function(e){o.observe(e)});})();
    </script>
@endsection
