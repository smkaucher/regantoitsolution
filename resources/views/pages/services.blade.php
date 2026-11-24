@extends('layouts.app')
@section('title', 'Services – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Development, design and branding, digital marketing, cyber security and social media management, all from one team.')
@section('content')
    @include('pages._head', ['title' => 'Our Services', 'crumb' => 'Services', 'lead' => 'Development, design, marketing, security and social media. One team, one point of contact.'])
    @include('partials.services-browser')
@endsection
