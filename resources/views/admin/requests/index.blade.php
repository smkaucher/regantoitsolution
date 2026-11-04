@extends('admin.layout')
@section('title', 'Service requests')
@section('content')
<nav class="ad-tabs">
    @php $all = $counts->sum(); @endphp
    <a href="{{ route('admin.requests.index', array_filter(['q' => $filters['q'], 'service' => $filters['service']])) }}" class="{{ !$filters['status'] ? 'on' : '' }}">All <b>{{ $all }}</b></a>
    @foreach(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'] as $k => $label)
        <a href="{{ route('admin.requests.index', array_filter(['status' => $k, 'q' => $filters['q'], 'service' => $filters['service']])) }}" class="{{ $filters['status'] === $k ? 'on' : '' }}">{{ $label }} <b>{{ $counts[$k] ?? 0 }}</b></a>
    @endforeach
</nav>
<form class="ad-filter" method="GET" action="{{ route('admin.requests.index') }}">
    @if($filters['status'])<input type="hidden" name="status" value="{{ $filters['status'] }}">@endif
    <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search name, email, phone, company, message">
    <select name="service">
        <option value="">All services</option>
        @foreach($services as $s)<option value="{{ $s->id }}" @selected($filters['service'] == $s->id)>{{ $s->title }}</option>@endforeach
    </select>
    <button class="ad-btn" type="submit">Filter</button>
    @if($filters['q'] || $filters['service'])<a href="{{ route('admin.requests.index', array_filter(['status' => $filters['status']])) }}">Clear</a>@endif
</form>
<section class="ad-panel">
    <table class="ad-table">
        <thead><tr><th>Name</th><th>Contact</th><th>Service</th><th>Status</th><th>Received</th><th></th></tr></thead>
        <tbody>
        @forelse($requests as $r)
            <tr>
                <td><a href="{{ route('admin.requests.show', $r) }}"><b>{{ $r->name }}</b></a>@if($r->company)<br><small>{{ $r->company }}</small>@endif</td>
                <td>{{ $r->email }}@if($r->phone)<br><small>{{ $r->phone }}</small>@endif</td>
                <td>{{ $r->service->title ?? '—' }}</td>
                <td><span class="ad-badge {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                <td title="{{ $r->created_at }}">{{ $r->created_at->diffForHumans() }}</td>
                <td class="ad-actions">
                    <a href="{{ route('admin.requests.show', $r) }}">View</a>
                    <form method="POST" action="{{ route('admin.requests.destroy', $r) }}" onsubmit="return confirm('Delete this request from {{ addslashes($r->name) }}?')">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="ad-empty">No requests match.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($requests->hasPages())<div class="ad-pager">{{ $requests->links('admin.pagination') }}</div>@endif
</section>
@endsection
