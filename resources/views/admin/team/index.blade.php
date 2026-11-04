@extends('admin.layout')
@section('title', 'IT Team')
@section('content')
<p class="ad-bar"><a class="ad-btn" href="{{ route('admin.team.create') }}">+ Add member</a></p>
<section class="ad-panel">
    <table class="ad-table">
        <thead><tr><th>Order</th><th>Member</th><th>Designation</th><th>Portfolio</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($members as $m)
            <tr>
                <td class="ad-move">
                    <form method="POST" action="{{ route('admin.team.move', $m) }}">@csrf @method('PATCH')<input type="hidden" name="dir" value="up"><button type="submit" title="Move up" @disabled($loop->first)>▲</button></form>
                    <form method="POST" action="{{ route('admin.team.move', $m) }}">@csrf @method('PATCH')<input type="hidden" name="dir" value="down"><button type="submit" title="Move down" @disabled($loop->last)>▼</button></form>
                </td>
                <td class="ad-who">
                    @if($m->photo)<img src="{{ \App\Support\Media::url($m->photo) }}" alt="">@else<i>{{ mb_substr($m->name, 0, 1) }}</i>@endif
                    <a href="{{ route('admin.team.edit', $m) }}"><b>{{ $m->name }}</b></a>
                </td>
                <td>{{ $m->designation }}</td>
                <td>@if($m->portfolio_url)<a href="{{ $m->portfolio_url }}" target="_blank" rel="noreferrer">Open ↗</a>@else—@endif</td>
                <td>
                    <form method="POST" action="{{ route('admin.team.toggle', $m) }}">@csrf @method('PATCH')
                        <button class="ad-badge {{ $m->is_active ? 'closed' : 'contacted' }}" type="submit" title="Click to toggle">{{ $m->is_active ? 'Visible' : 'Hidden' }}</button>
                    </form>
                </td>
                <td class="ad-actions">
                    <a href="{{ route('admin.team.edit', $m) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.team.destroy', $m) }}" onsubmit="return confirm('Delete {{ addslashes($m->name) }}?')">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="ad-empty">No team members yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
<p class="ad-hint">Use ▲ ▼ to change the order shown on the Team page and the About page.</p>
@endsection
