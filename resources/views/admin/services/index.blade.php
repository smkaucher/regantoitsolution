@extends('admin.layout')
@section('title', 'Services')
@section('content')
<p class="ad-bar"><a class="ad-btn" href="{{ route('admin.services.create') }}">+ Add service</a></p>
<section class="ad-panel">
    <table class="ad-table">
        <thead><tr><th>Order</th><th>Title</th><th>Items</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($services as $s)
            <tr>
                <td>{{ $s->sort_order }}</td>
                <td><a href="{{ route('admin.services.edit', $s) }}"><b>{{ $s->title }}</b></a><br><small>/services/{{ $s->slug }}</small></td>
                <td>{{ $s->items_count }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.services.toggle', $s) }}">@csrf @method('PATCH')
                        <button class="ad-badge {{ $s->is_active ? 'closed' : 'contacted' }}" type="submit" title="Click to toggle">{{ $s->is_active ? 'Visible' : 'Hidden' }}</button>
                    </form>
                </td>
                <td class="ad-actions">
                    <a href="{{ route('admin.services.edit', $s) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.services.destroy', $s) }}" data-confirm-title="Delete this service?" data-confirm="“{{ $s->title }}”, every service inside it and their pictures will be removed from the website and the menu. This can't be undone.">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="ad-empty">No services yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
