@php
    $points = $item->points ?? [];
    $lead = $item->description ?: ($item->title . ' from the Reganto IT Solution team. Everything that is included is listed below.');
    // The item's own picture, else its service's image; the icon only when neither has one.
    $cover = \App\Support\Media::url($item->image ?: $service->image);
    $wa = $site['whatsapp'] ?? null;
    if ($wa) { $wa .= (str_contains($wa, '?') ? '&' : '?') . 'text=' . rawurlencode("Hi, I'd like to book: " . $item->title); }
    $mail = $site['email'] ?? null;
    $tel = !empty($site['phone']) ? preg_replace('/[^\d+]/', '', $site['phone']) : null;
@endphp
<div class="sp-scroll">
    <div class="sp-top {{ $cover ? '' : 'is-plain' }}">
        @if($cover)
            <img src="{{ $cover }}" alt="" width="960" height="600" decoding="async">
        @else
            <x-icon :name="$service->icon ?: 'rocket'" size="40" />
        @endif
    </div>
    <div class="sp-body">
        <span class="sp-badge">{{ $service->title }}</span>
        <h2 id="spTitle">{{ $item->title }}</h2>
        <p class="sp-lead">{{ $lead }}</p>
        @if($points)
            <h3 class="sp-h">What we deliver <span>{{ count($points) }} included</span></h3>
            <ul class="sp-points">
                @foreach($points as $p)
                    <li><x-icon :name="\App\Support\PointIcon::for($p)" size="14" /> {{ $p }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
<div class="sp-foot">
    @if($wa)<a class="sp-btn wa" href="{{ $wa }}" target="_blank" rel="noreferrer"><x-icon name="whatsapp" size="16" /> WhatsApp</a>@endif
    @if($mail)<a class="sp-btn" href="mailto:{{ $mail }}?subject={{ rawurlencode('Booking: ' . $item->title) }}"><x-icon name="mail" size="16" /> Email</a>@endif
    @if($tel)<a class="sp-btn" href="tel:{{ $tel }}"><x-icon name="phone" size="16" /> Call</a>@endif
</div>
