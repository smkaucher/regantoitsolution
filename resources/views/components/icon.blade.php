@props(['name', 'size' => 18])
@php
    $paths = [
        'cloud' => '<path d="M7 18a4 4 0 010-8 5.5 5.5 0 0110.6 1.5A3.3 3.3 0 0117 18z"/>',
        'hex' => '<path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/>',
        'leaf' => '<path d="M5 19c0-9 5-14 14-14 0 9-5 14-14 14zM5 19l7-7"/>',
        'wave' => '<path d="M3 12c3-6 6-6 9 0s6 6 9 0"/>',
        'gear' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2"/>',
        'ring' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/>',
        'chart' => '<rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 15l3-4 3 2 3-5"/>',
        'user' => '<rect x="4" y="4" width="16" height="16" rx="3"/><circle cx="12" cy="10" r="2.5"/><path d="M7.5 17c1-2.5 8-2.5 9 0"/>',
        'users' => '<circle cx="9" cy="8" r="3.2"/><path d="M3 20c.8-4.5 11.2-4.5 12 0"/><path d="M16 5a3 3 0 010 6M18 15c2 .6 3 2.3 3 5"/>',
        'arrow-circle' =>'<circle cx="12" cy="12" r="10"/><path d="M8 12h8M13 8l4 4-4 4"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'check' => '<path d="M5 12l4.5 4.5L19 7"/>',
        'chevron' => '<path d="M9 6l6 6-6 6"/>',
        'layers' => '<path d="M12 3l9 5-9 5-9-5zM3 12.5l9 5 9-5M3 17l9 5 9-5"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M3.5 7l8.5 6 8.5-6"/>',
        'phone' => '<path d="M5 4h3.5l1.8 4.6-2.2 1.4a11 11 0 005.9 5.9l1.4-2.2L20 15.5V19a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/>',
        'whatsapp' => '<path d="M21 11.5a8.5 8.5 0 01-12.6 7.4L3 20.5l1.7-5A8.5 8.5 0 1121 11.5z"/><path d="M9 8.8c.3 3 2.2 5 5.2 5.4l1.2-1.4-2-1-.8.7a4 4 0 01-1.9-1.9l.7-.8-1-2z"/>',
        'shield' => '<path d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6z"/>',
        'sparkle' => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5L18 18M18 6l-2.5 2.5M8.5 15.5L6 18"/>',
        'rocket' => '<path d="M12 15l-3-3a22 22 0 012-4 12 12 0 019-5c0 3-1 8-5 11a22 22 0 01-4 2zM9 12H4s.5-3 2-4c1.5-1 5 0 5 0M12 15v5s3-.5 4-2c1-1.5 0-5 0-5"/>',
        'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r=".6" fill="currentColor"/>',
        'youtube' => '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.5v5l4.5-2.5z" fill="currentColor"/>',
        'tiktok' => '<path d="M14 4v10.5a3.5 3.5 0 11-3.5-3.5M14 4c.3 2.3 1.9 3.9 4.5 4.1"/>',
        'dribbble' => '<circle cx="12" cy="12" r="9.5"/><path d="M5.5 6.5c5 .8 9.5 3.3 13 9M9 3c3 3.5 5 8 5.5 17.5M2.8 11.3c5.3.6 11-.6 17.4 1.7"/>',
    ];
    $filled = [
        'star' => '<path d="M12 2.8l2.8 5.8 6.4.9-4.6 4.5 1.1 6.3L12 17.3 6.3 20.3l1.1-6.3L2.8 9.5l6.4-.9z"/>',
        'facebook' => '<path d="M14 8h2.2V5h-2.6C11.3 5 10 6.4 10 8.7V11H8v3h2v6h3.2v-6h2.3l.5-3h-2.8V9c0-.6.3-1 1-1z"/>',
        'linkedin' => '<rect x="4" y="9" width="3" height="11"/><circle cx="5.5" cy="5.6" r="1.8"/><path d="M10 9h3v1.6c.6-1 1.7-1.8 3.2-1.8 2.6 0 3.8 1.6 3.8 4.4V20h-3v-5.8c0-1.5-.6-2.4-1.9-2.4s-2.1 1-2.1 2.4V20h-3z"/>',
    ];
@endphp
@if(isset($filled[$name]))
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="currentColor" stroke="none" {{ $attributes }}>{!! $filled[$name] !!}</svg>
@else
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" {{ $attributes }}>{!! $paths[$name] ?? '' !!}</svg>
@endif
