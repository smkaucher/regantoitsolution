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
<section class="wy" id="features" data-replay data-threshold="0.2">
    <div class="wy-head">
        <span class="svc2-eyebrow" data-a="up">WHY CHOOSE US</span>
        <h2 data-a="up" style="--d:.1s">Why Businesses Choose {{ $name }}</h2>
        <p class="wy-tag" data-a="up" style="--d:.2s">From first idea to <em>final experience.</em></p>
    </div>

    <div class="wy-cards">
        @foreach($features as $i => [$icon, $title, $text])
            <article data-a="up" style="--d:{{ .25 + $i * .1 }}s">
                <span class="wy-ico"><x-icon :name="$icon" size="22" /></span>
                <h3>{{ $title }}</h3>
                <p>{{ $text }}</p>
            </article>
        @endforeach
    </div>

    <ul class="wy-list">
        @foreach($reasons as $i => $text)
            <li data-a="up" style="--d:{{ .6 + $i * .08 }}s">
                <span class="wy-check"><x-icon name="check" size="14" /></span>{{ $text }}
            </li>
        @endforeach
    </ul>

    <p class="wy-cta" data-a="up" style="--d:1.1s"><a class="btn" href="#work">Explore our work <x-icon name="arrow-circle" size="16" /></a></p>
</section>
