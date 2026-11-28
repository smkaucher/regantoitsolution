@php
    $name = $site['site_name'] ?? 'Reganto IT Solution';
    $features = [
        ['chart', 'Websites that feel alive', 'High-end interfaces with thoughtful motion, strong storytelling and conversion-focused UX.'],
        ['user', 'Products people want to use', 'We turn complicated workflows into simple, beautiful and scalable digital products.'],
        ['users', 'SaaS & Platforms', 'Scalable dashboards, admin systems, reseller platforms, automation and powerful backends.'],
        ['hex', 'Brands with a point of view', 'Visual identities, digital systems and brand experiences designed to stay recognizable everywhere.'],
    ];
    $reasons = ['Strategy-Led UI/UX That Converts', 'Custom Builds Tailored to Your Goals', 'Transparent Communication & Process', 'Scalable Frontend & Backend Engineering', 'Performance, Speed & Quality Standards', 'Reliable Support & Long-Term Partnership'];
@endphp
{{-- Why choose us. On scroll-in (.in): a teal rule draws across the top of each column in turn, the icons turn in,
     then the check marks of the reasons draw themselves one by one. --}}
<section class="hm hm-why" data-replay data-threshold="0.2">
    <div class="hm-head">
        <p class="hm-eb"><i></i>[ WHY CHOOSE US ]</p>
        <h2 class="hm-h2">Why businesses choose <em>{{ $name }}.</em></h2>
        <p class="hm-sub">From first idea to final experience.</p>
    </div>

    <div class="hw-cols">
        @foreach($features as $i => [$icon, $title, $text])
            <article style="--k:{{ $i }}">
                <span class="hw-ico"><x-icon :name="$icon" size="22" /></span>
                <h3>{{ $title }}</h3>
                <p>{{ $text }}</p>
            </article>
        @endforeach
    </div>

    <ul class="hw-list">
        @foreach($reasons as $i => $text)
            <li style="--k:{{ $i }}">
                <svg class="hw-tick" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5" pathLength="1" /></svg>{{ $text }}
            </li>
        @endforeach
    </ul>

    <p class="hm-cta"><a class="hm-btn" href="#work">Explore our work <span><x-icon name="arrow-right" size="14" /></span></a></p>
</section>
