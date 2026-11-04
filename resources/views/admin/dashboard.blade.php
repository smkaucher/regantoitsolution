@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
    <section class="ad-stats">
        @foreach($counts as [$label, $n, $key])
            <a class="ad-stat" href="{{ route($key === 'team' ? 'admin.team.index' : 'admin.' . $key . '.index', $key === 'requests' ? ['status' => 'new'] : []) }}"><b>{{ $n }}</b><span>{{ $label }}</span></a>
        @endforeach
    </section>
    <section class="ad-panel">
        <h2>Latest service requests <a class="ad-more" href="{{ route('admin.requests.index') }}">View all</a></h2>
        @if($latest->count())
            <table class="ad-table">
                <thead><tr><th>Name</th><th>Email</th><th>Service</th><th>Status</th><th>Received</th></tr></thead>
                <tbody>
                @foreach($latest as $r)
                    <tr>
                        <td><a href="{{ route('admin.requests.show', $r) }}">{{ $r->name }}</a></td>
                        <td>{{ $r->email }}</td>
                        <td>{{ $r->service->title ?? '—' }}</td>
                        <td><span class="ad-badge {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
                        <td>{{ $r->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="ad-empty">No requests yet.</p>
        @endif
    </section>
@endsection
