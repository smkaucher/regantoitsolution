@extends('admin.layout')
@section('title', 'Projects')
@section('content')
<p class="ad-bar"><a class="ad-btn" href="{{ route('admin.projects.create') }}">+ Add project</a></p>
<section class="ad-panel">
    <table class="ad-table">
        <thead><tr><th>Order</th><th>Project</th><th>Category</th><th>Gallery</th><th>Featured</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @forelse($projects as $p)
            <tr>
                <td>{{ $p->sort_order }}</td>
                <td><a href="{{ route('admin.projects.edit', $p) }}"><b>{{ $p->title }}</b></a><br><small>/projects/{{ $p->slug }}</small></td>
                <td>{{ $p->category ?: '—' }}</td>
                <td>{{ $p->images_count }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.projects.toggle', $p) }}">@csrf @method('PATCH')<input type="hidden" name="field" value="featured">
                        <button class="ad-badge {{ $p->is_featured ? 'closed' : 'contacted' }}" type="submit" title="Click to toggle">{{ $p->is_featured ? 'Featured' : 'No' }}</button>
                    </form>
                </td>
                <td>
                    <form method="POST" action="{{ route('admin.projects.toggle', $p) }}">@csrf @method('PATCH')<input type="hidden" name="field" value="active">
                        <button class="ad-badge {{ $p->is_active ? 'closed' : 'contacted' }}" type="submit" title="Click to toggle">{{ $p->is_active ? 'Visible' : 'Hidden' }}</button>
                    </form>
                </td>
                <td class="ad-actions">
                    <a href="{{ route('admin.projects.edit', $p) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.projects.destroy', $p) }}" onsubmit="return confirm('Delete {{ addslashes($p->title) }} and its photos?')">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="ad-empty">No projects yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
<p class="ad-hint">Featured projects appear in the home page "Recent Work" section (if none are featured, all visible projects are shown). All visible projects appear on the Projects page.</p>
@endsection
