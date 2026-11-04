@extends('admin.layout')
@section('title', $cfg['label'])
@section('content')
@php
    $imgField = collect($cfg['fields'])->filter(fn ($d) => $d[1] === 'image')->keys()->first();
    $cell = function ($item, $col) {
        $v = $item->{$col};
        if ($col === 'published_at') return $v ? $v->format('M j, Y') : '—';
        if ($col === 'rating') return str_repeat('★', (int) $v);
        if ($col === 'value') return rtrim(rtrim(number_format((float) $v, 1, '.', ''), '0'), '.');
        return $v !== null && $v !== '' ? $v : '—';
    };
@endphp
<p class="ad-bar"><a class="ad-btn" href="{{ route('admin.content.create', $type) }}">+ Add {{ $cfg['singular'] }}</a></p>
<section class="ad-panel">
    <table class="ad-table">
        <thead><tr>
            @unless(!empty($cfg['no_order']))<th>Order</th>@endunless
            <th>{{ Str::headline($cfg['title']) }}</th>
            @foreach($cfg['cols'] as $label)<th>{{ $label }}</th>@endforeach
            <th>Status</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                @unless(!empty($cfg['no_order']))<td>{{ $item->sort_order }}</td>@endunless
                <td class="ad-who">
                    @if($imgField && $item->{$imgField})<img src="{{ \App\Support\Media::url($item->{$imgField}) }}" alt="">@endif
                    <a href="{{ route('admin.content.edit', [$type, $item->id]) }}"><b>{{ Str::limit($item->{$cfg['title']}, 70) }}</b></a>
                </td>
                @foreach($cfg['cols'] as $col => $label)<td>{{ $cell($item, $col) }}</td>@endforeach
                <td>
                    <form method="POST" action="{{ route('admin.content.toggle', [$type, $item->id]) }}">@csrf @method('PATCH')
                        <button class="ad-badge {{ $item->is_active ? 'closed' : 'contacted' }}" type="submit" title="Click to toggle">{{ $item->is_active ? 'Visible' : 'Hidden' }}</button>
                    </form>
                </td>
                <td class="ad-actions">
                    <a href="{{ route('admin.content.edit', [$type, $item->id]) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.content.destroy', [$type, $item->id]) }}" onsubmit="return confirm('Delete this {{ $cfg['singular'] }}?')">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ 3 + count($cfg['cols']) + (empty($cfg['no_order']) ? 1 : 0) }}" class="ad-empty">Nothing here yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
