@php
    $verified = 'Verified <svg class="fb-verified" viewBox="0 0 24 24" width="22" height="22" role="img" aria-label="Verified on Facebook"><circle cx="12" cy="12" r="10.5" fill="#1877F2"/><path d="M7.6 12.4l3 3 5.8-6.1" fill="none" stroke="#fff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $badges = [['4.9', 'Rating · Clutch Reviewed'], ['USA · Dubai', 'Based in'], ['10M', 'Views · Behance Most View'], ['10M', 'Views · Dribbble Most View'], [$verified, 'Facebook']];
@endphp
<section class="block leader" data-replay data-threshold="0.3">
    <div>
        <h2 class="lead-title">{{ strtoupper($site['site_name'] ?? 'Reganto IT Solution') }}’S 3 Years of Innovation</h2>
        <div class="badges">
            @foreach($badges as $i => [$n, $l])
                <div style="--i:{{ $i }}"><strong>{!! $n !!}</strong>{{ $l }}</div>
            @endforeach
        </div>
        <p class="chips">
            @foreach(['24/7 SUPPORT', 'WEEKLY UPDATES', 'SECURE & COMPLIANT', '99.9% UPTIME'] as $i => $c)
                <span style="--i:{{ $i }}"><x-icon name="check" size="12" /> {{ $c }}</span>
            @endforeach
        </p>
    </div>
</section>
