@extends('admin.layout')
@section('title', 'Request from ' . $r->name)
@section('content')
<p class="ad-bar"><a href="{{ route('admin.requests.index') }}">← All requests</a></p>
<div class="ad-split">
    <section class="ad-panel">
        <h2>Request</h2>
        <dl class="ad-dl">
            <dt>Name</dt><dd>{{ $r->name }}</dd>
            <dt>Email</dt><dd><a href="mailto:{{ $r->email }}">{{ $r->email }}</a></dd>
            <dt>Phone</dt><dd>@if($r->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $r->phone) }}">{{ $r->phone }}</a>@else—@endif</dd>
            <dt>Company</dt><dd>{{ $r->company ?: '—' }}</dd>
            <dt>Service</dt><dd>{{ $r->service->title ?? '—' }}</dd>
            <dt>Budget</dt><dd>{{ $r->budget ?: '—' }}</dd>
            <dt>Received</dt><dd>{{ $r->created_at->format('M j, Y g:i A') }} ({{ $r->created_at->diffForHumans() }})</dd>
        </dl>
        <h2>Message</h2>
        <p class="ad-msg">{{ $r->message }}</p>
    </section>
    <form class="ad-panel ad-form" method="POST" action="{{ route('admin.requests.update', $r) }}">
        @csrf @method('PUT')
        <h2>Follow-up</h2>
        <label>Status
            <select name="status">
                @foreach(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $k => $label)
                    <option value="{{ $k }}" @selected(old('status', $r->status) === $k)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')<em>{{ $message }}</em>@enderror
        </label>
        <label>Internal note (not shown to the visitor)
            <textarea name="admin_note" rows="6">{{ old('admin_note', $r->admin_note) }}</textarea>
            @error('admin_note')<em>{{ $message }}</em>@enderror
        </label>
        <button class="ad-btn" type="submit">Save</button>
    </form>
</div>
@endsection
