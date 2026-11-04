@php
    $amb = [[80, '+', 0, 'Projects Delivered'], [40, '+', 0, 'Happy Clients'], [4.9, '★', 1, 'Client Rating'], [24, '/7', 0, 'Digital Support']];
@endphp
<section class="amb" data-replay data-threshold="0.35">
    <small class="amb-label" data-a="left">BUILT FOR AMBITION</small>
    @foreach($amb as $k => [$v, $suffix, $dec, $label])
        <div class="amb-item" data-a="scale" style="--d:{{ 0.19 + $k * 0.14 }}s">
            <strong><span data-count="{{ $v }}" data-suffix="{{ $suffix }}" data-decimals="{{ $dec }}">0</span></strong>
            {{ $label }}
        </div>
    @endforeach
</section>
