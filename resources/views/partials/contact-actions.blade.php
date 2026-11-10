@php
    $svc = $service ?? null;
    $wa = $site['whatsapp'] ?? null;
    if ($wa && $svc) { $wa .= (str_contains($wa, '?') ? '&' : '?') . 'text=' . rawurlencode("Hi, I'd like to book: " . $svc->title); }
    $mail = $site['email'] ?? null;
    $tel = !empty($site['phone']) ? preg_replace('/[^\d+]/', '', $site['phone']) : null;
@endphp
<div class="cta-cards">
    @if($wa)<a class="cta-card wa" href="{{ $wa }}" target="_blank" rel="noreferrer"><span class="cta-ic"><x-icon name="whatsapp" size="22" /></span><b>WhatsApp</b><small>Chat with us now</small></a>@endif
    @if($mail)<a class="cta-card" href="mailto:{{ $mail }}@if($svc)?subject={{ rawurlencode('Booking: ' . $svc->title) }}@endif"><span class="cta-ic"><x-icon name="mail" size="22" /></span><b>Email</b><small>{{ $mail }}</small></a>@endif
    @if($tel)<a class="cta-card" href="tel:{{ $tel }}"><span class="cta-ic"><x-icon name="phone" size="22" /></span><b>Phone call</b><small>{{ $site['phone'] }}</small></a>@endif
</div>
