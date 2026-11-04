@php
    $icons = ['cloud', 'hex', 'leaf', 'wave', 'gear', 'ring'];
@endphp
@if($clients->count())
<section class="brand-marquee" aria-label="Trusted by teams that value quality">
    <div class="brand-track">
        @for($c = 0; $c < 6; $c++)
            @foreach($clients as $i => $client)
                <span class="brand-item" @if($c > 0) aria-hidden="true" @endif>
                    @if($client->logo)<img class="brand-logo" src="{{ \App\Support\Media::url($client->logo) }}" alt="">@else<x-icon :name="$icons[$i % 6]" />@endif {{ $client->name }}
                </span>
            @endforeach
        @endfor
    </div>
</section>
@endif
