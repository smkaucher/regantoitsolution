@extends('admin.layout')
@section('title', $cfg['label'])
@section('content')
@php
    $imgField = collect($cfg['fields'])->filter(fn ($d) => $d[1] === 'image')->keys()->first();
    $cell = function ($item, $col) {
        $v = $item->{$col};
        if ($col === 'rating') return str_repeat('★', (int) $v);
        if ($col === 'source') return $v === 'visitor' ? 'Website visitor · ' . $item->created_at?->format('M j, Y') : 'Admin';
        if ($col === 'value') return rtrim(rtrim(number_format((float) $v, 1, '.', ''), '0'), '.');
        return $v !== null && $v !== '' ? $v : '—';
    };
@endphp
<p class="ad-bar"><a class="ad-btn" href="{{ route('admin.content.create', $type) }}">+ Add {{ $cfg['singular'] }}</a></p>
<section class="ad-panel">
    <table class="ad-table">
        <thead><tr>
            <th>Order</th>
            <th>{{ Str::headline($cfg['title']) }}</th>
            @foreach($cfg['cols'] as $label)<th>{{ $label }}</th>@endforeach
            <th>Status</th><th></th>
        </tr></thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->sort_order }}</td>
                <td class="ad-who">
                    @if($imgField && $item->{$imgField})<img src="{{ \App\Support\Media::url($item->{$imgField}) }}" alt="">@endif
                    <a href="{{ route('admin.content.edit', [$type, $item->id]) }}"><b>{{ Str::limit($item->{$cfg['title']}, 70) }}</b></a>
                </td>
                @foreach($cfg['cols'] as $col => $label)<td>{{ $cell($item, $col) }}</td>@endforeach
                <td>
                    <form method="POST" action="{{ route('admin.content.toggle', [$type, $item->id]) }}">@csrf @method('PATCH')
                        @if($type === 'testimonials' && $item->isWaiting())
                            <button class="ad-badge wait" type="submit" title="Sent from the website. Click to show it on the site.">Waiting · Show</button>
                        @else
                            <button class="ad-badge {{ $item->is_active ? 'closed' : 'contacted' }}" type="submit" title="Click to toggle">{{ $item->is_active ? 'Visible' : 'Hidden' }}</button>
                        @endif
                    </form>
                </td>
                <td class="ad-actions">
                    <a href="{{ route('admin.content.edit', [$type, $item->id]) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.content.destroy', [$type, $item->id]) }}" data-confirm-title="Delete this {{ $cfg['singular'] }}?" data-confirm="“{{ Str::limit($item->{$cfg['title']}, 60) }}” will be removed from the website{{ $imgField ? ', together with its picture' : '' }}. This can't be undone.">@csrf @method('DELETE')<button class="ad-link-danger" type="submit">Delete</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ 4 + count($cfg['cols']) }}" class="ad-empty">Nothing here yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</section>
@endsection
