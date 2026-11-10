@php
    $soc = [
        ['Facebook', 'facebook', $site['facebook'] ?? null],
        ['Instagram', 'instagram', $site['instagram'] ?? null],
        ['YouTube', 'youtube', $site['youtube'] ?? null],
        ['TikTok', 'tiktok', $site['tiktok'] ?? null],
        ['LinkedIn', 'linkedin', $site['linkedin'] ?? null],
    ];
    $tel = preg_replace('/[^\d+]/', '', $site['phone'] ?? '');
@endphp
<footer class="closing" id="contact">
    <div class="closing-top">
        <div class="closing-copy">
            <h2>Let’s Build Something Bold — Together</h2>
            <p>Book a FREE 15-minute strategy call with our expert team in the USA and globally to get personalized guidance in minutes.</p>
            <div class="closing-actions">
                <a class="btn" href="{{ $site['book_call_url'] ?? url('/contact') }}">Book Your FREE Strategy Call <x-icon name="arrow-right" /></a>
                <a class="btn ghost-l" href="{{ url('/services') }}">View Services <x-icon name="layers" /></a>
            </div>
            <div class="closing-pills">
                <span><x-icon name="shield" size="15" /> Privacy-first</span>
                <span><x-icon name="sparkle" size="15" /> Modern UI</span>
                <span><x-icon name="rocket" size="15" /> Fast Delivery</span>
            </div>
        </div>
        <aside class="expert">
            <div class="expert-head"><i><x-icon name="star" size="20" /></i><div><b>Talk to an Expert</b><small>Response within 30 minutes</small></div></div>
            <a href="mailto:{{ $site['email'] ?? '' }}"><x-icon name="mail" /> Email Us</a>
            <a href="tel:{{ $tel }}"><x-icon name="phone" /> Call Now</a>
            <a href="{{ $site['whatsapp'] ?? '#' }}"><x-icon name="whatsapp" /> WhatsApp</a>
            <p class="expert-live"><i></i> Available: 24/7</p>
        </aside>
    </div>
    <div class="closing-cols">
        <div class="closing-brand">
            @if(!empty($site['logo']))<img src="{{ $site['logo'] }}" alt="{{ $site['site_name'] ?? '' }}">@endif
            <b>Design • Development • Growth</b>
            <p>We craft clean UI/UX and high-performing websites for startups and businesses across United States and beyond.</p>
            <div class="closing-soc">
                @foreach($soc as [$n, $ic, $href])
                    @if($href)<a href="{{ $href }}" aria-label="{{ $n }}" title="{{ $n }}" target="_blank" rel="noreferrer"><x-icon :name="$ic" /></a>@endif
                @endforeach
            </div>
        </div>
        <div>
            <h4>Quick Links</h4>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/projects') }}">Projects</a>
            <a href="{{ url('/about') }}">About</a>
            <a href="{{ url('/blog') }}">Blog</a>
        </div>
        <div>
            <h4>Services</h4>
            @foreach($navServices as $s)<a href="{{ url('/services/' . $s->slug) }}">{{ $s->title }}</a>@endforeach
        </div>
        <div>
            <h4>Others</h4>
            <a href="{{ url('/contact') }}">Book a Service</a>
            <a href="mailto:{{ $site['email'] ?? '' }}">{{ $site['email'] ?? '' }}</a>
            <a href="tel:{{ $tel }}">{{ $site['phone'] ?? '' }}</a>
            <a href="{{ config('site.map_url') }}" target="_blank" rel="noreferrer">{{ $site['address'] ?: config('site.address') }}</a>
        </div>
    </div>
    <div class="closing-bar"><small>© {{ date('Y') }} {{ $site['footer_text'] ?? 'Reganto IT Solution. All Rights Reserved.' }}</small><a href="{{ url('/contact') }}">Contact</a></div>
</footer>
