@extends('admin.layout')
@section('title', 'Team')
@section('content')
<p class="ad-bar">
    <a class="ad-btn" href="{{ route('admin.team.groups.create') }}">+ Add section</a>
    <a class="ad-btn ghost" style="margin-top:0" href="{{ url('/team') }}" target="_blank" rel="noreferrer">View Team page ↗</a>
</p>
<p class="ad-hint" style="margin:-.2rem 0 1.2rem">The Team page shows each visible section in this order, with its cover picture on every card. The About page shows the first four people.</p>

@forelse($groups as $g)
    @php $cover = \App\Support\Media::url($g->cover); @endphp
    <section class="ad-panel tmg{{ $g->is_active ? '' : ' is-off' }}">
        <header class="tmg-head">
            <span class="tmg-cover"@if($cover) style="background-image:url('{{ $cover }}')"@endif>@unless($cover) No cover @endunless</span>
            <div class="tmg-title">
                <h2>{{ $g->name }}</h2>
                <small>{{ $g->members->count() }} {{ Str::plural('person', $g->members->count()) }} · order {{ $g->sort_order }}</small>
            </div>
            <div class="ad-actions">
                <form method="POST" action="{{ route('admin.team.groups.toggle', $g) }}">@csrf @method('PATCH')
                    <button class="ad-badge {{ $g->is_active ? 'closed' : 'contacted' }}" type="submit" title="Click to show or hide this section">{{ $g->is_active ? 'Visible' : 'Hidden' }}</button>
                </form>
                <a href="{{ route('admin.team.groups.edit', $g) }}">Edit section</a>
                <form method="POST" action="{{ route('admin.team.groups.destroy', $g) }}" data-confirm-title="Delete this section?" data-confirm="“{{ $g->name }}” and the {{ $g->members->count() }} {{ Str::plural('person', $g->members->count()) }} in it will be removed from the Team page. This can't be undone.">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
            </div>
        </header>

        <table class="ad-table">
            <tbody>
            @forelse($g->members as $m)
                @php $photo = \App\Support\Media::url($m->photo); $links = collect([$m->portfolio_url, $m->linkedin_url, $m->github_url, $m->facebook_url])->filter()->count(); @endphp
                <tr>
                    <td class="ad-who">
                        @if($photo)<img src="{{ $photo }}" alt="">@else<i>{{ mb_strtoupper(mb_substr($m->name, 0, 1)) }}</i>@endif
                        <a href="{{ route('admin.team.members.edit', $m) }}"><b>{{ $m->name }}</b></a>
                    </td>
                    <td>{{ $m->designation ?: '—' }}</td>
                    <td class="tmg-links">{{ $links ? $links . ' ' . Str::plural('link', $links) : 'No links' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.team.members.toggle', $m) }}">@csrf @method('PATCH')
                            <button class="ad-badge {{ $m->is_active ? 'closed' : 'contacted' }}" type="submit" title="Click to show or hide">{{ $m->is_active ? 'Visible' : 'Hidden' }}</button>
                        </form>
                    </td>
                    <td class="ad-actions">
                        <a href="{{ route('admin.team.members.edit', $m) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.team.members.destroy', $m) }}" data-confirm-title="Remove this person?" data-confirm="{{ $m->name }} will be removed from “{{ $g->name }}” on the Team page. This can't be undone.">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td class="ad-empty">No one in this section yet. It stays off the Team page until someone is added.</td></tr>
            @endforelse
            </tbody>
        </table>
        <a class="ad-btn ghost tmg-add" href="{{ route('admin.team.members.create', ['group' => $g->id]) }}">+ Add person to {{ $g->name }}</a>
    </section>
@empty
    <section class="ad-panel"><p class="ad-empty">No team sections yet. Add a section (for example “Software Engineer”), then add the people in it.</p></section>
@endforelse
@endsection
