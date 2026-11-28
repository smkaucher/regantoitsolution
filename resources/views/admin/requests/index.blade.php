@extends('admin.layout')
@section('title', 'Project requests')
@section('content')
<p class="ad-bar">
    <a class="ad-btn{{ $status ? ' ghost' : '' }}" href="{{ route('admin.requests.index') }}">All ({{ $counts->except('spam')->sum() }})</a>
    @foreach(\App\Models\ProjectRequest::STATUSES as $key => $label)
        <a class="ad-btn{{ $status === $key ? '' : ' ghost' }}" href="{{ route('admin.requests.index', ['status' => $key]) }}">{{ $label }} ({{ $counts[$key] ?? 0 }})</a>
    @endforeach
</p>
<section class="ad-panel">
    <table class="ad-table">
        <thead><tr><th>Received</th><th>Name</th><th>Phone</th><th>Building</th><th>Budget</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($items as $r)
            <tr>
                <td>{{ $r->created_at->timezone('Asia/Dhaka')->format('M j, Y g:i A') }}</td>
                <td class="ad-who"><a href="{{ route('admin.requests.show', $r) }}"><b>{{ $r->name }}</b></a>@if($r->company)<br><small>{{ $r->company }}</small>@endif</td>
                <td><a href="tel:{{ $r->phone }}">{{ $r->phone }}</a></td>
                <td>{{ $r->services ? implode(', ', $r->services) : '—' }}</td>
                <td>{{ $r->budget ?: '—' }}</td>
                <td><span class="ad-badge {{ $r->status }}">{{ \App\Models\ProjectRequest::STATUSES[$r->status] ?? $r->status }}</span></td>
                <td class="ad-actions"><a href="{{ route('admin.requests.show', $r) }}">Open</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="ad-empty">No project requests yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
{{ $items->links('admin.pagination') }}
@endsection
