@extends('layouts.app')
@section('title', 'Our Team – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Meet the designers and engineers behind every project.')
@section('content')
    @include('pages._head', ['title' => 'Our IT Team', 'crumb' => 'Team', 'lead' => 'The designers and engineers behind every project.'])
    <section class="block">@if($team->count())@include('pages._team')@else<p class="sub">Team members will appear here soon.</p>@endif</section>
@endsection
