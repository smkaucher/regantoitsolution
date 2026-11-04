@extends('layouts.app')
@section('title', 'Contact Us – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Tell us about your project and request a service. We reply within 30 minutes.')
@section('content')
    @include('pages._head', ['title' => 'Contact Us', 'crumb' => 'Contact', 'lead' => 'Tell us about your project. We reply within 30 minutes.'])
    <section class="block pg-split">
        <div>
            <h3>Request a Service</h3>
            @if(session('sent'))
                <div class="pg-ok" role="status">Thank you! Your request has been received. We will contact you shortly.</div>
            @endif
            <form class="pg-form" method="POST" action="{{ route('contact.submit') }}" novalidate>
                @csrf
                <input type="text" name="website" class="pg-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="pg-row">
                    <label>Name *<input type="text" name="name" value="{{ old('name') }}" required>@error('name')<em>{{ $message }}</em>@enderror</label>
                    <label>Email *<input type="email" name="email" value="{{ old('email') }}" required>@error('email')<em>{{ $message }}</em>@enderror</label>
                </div>
                <div class="pg-row">
                    <label>Phone<input type="text" name="phone" value="{{ old('phone') }}">@error('phone')<em>{{ $message }}</em>@enderror</label>
                    <label>Company<input type="text" name="company" value="{{ old('company') }}">@error('company')<em>{{ $message }}</em>@enderror</label>
                </div>
                <div class="pg-row">
                    <label>Service
                        <select name="service_id">
                            <option value="">Select a service</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}" @selected(old('service_id', request('service')) == $s->id)>{{ $s->title }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Budget
                        <select name="budget">
                            <option value="">Select a range</option>
                            @foreach(['Under $1,000', '$1,000 – $5,000', '$5,000 – $15,000', '$15,000+'] as $b)
                                <option @selected(old('budget') == $b)>{{ $b }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <label>Message *<textarea name="message" rows="5" required>{{ old('message') }}</textarea>@error('message')<em>{{ $message }}</em>@enderror</label>
                <button class="btn" type="submit">Send request <x-icon name="arrow-right" /></button>
            </form>
        </div>
        <aside class="pg-side">
            <h4>Get in touch</h4>
            <ul>
                @if(!empty($site['email']))<li><b>Email</b><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></li>@endif
                @if(!empty($site['phone']))<li><b>Phone</b><a href="tel:{{ preg_replace('/[^\d+]/', '', $site['phone']) }}">{{ $site['phone'] }}</a></li>@endif
                @if(!empty($site['address']))<li><b>Address</b><span>{{ $site['address'] }}</span></li>@endif
            </ul>
        </aside>
    </section>
@endsection
