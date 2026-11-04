@php
    $features = [
        ['chart', 'Websites that feel alive', 'High-end interfaces with thoughtful motion, strong storytelling and conversion-focused UX.', true],
        ['user', 'Products people want to use', 'We turn complicated workflows into simple, beautiful and scalable digital products.', false],
        ['users', 'SaaS & Platforms', 'Scalable dashboards, admin systems, reseller platforms, automation and powerful backends.', false],
        ['hex', 'Brands with a point of view', 'Visual identities, digital systems and brand experiences designed to stay recognizable everywhere.', false],
    ];
@endphp
<section class="features" id="features" data-replay data-threshold="0.3">
    <div class="features-copy">
        <span class="chip" data-a="up" style="--d:.05s"><i></i> What We Build</span>
        <h2 data-words data-stagger="0.05">From first idea to final experience.</h2>
        <p data-words data-stagger="0.03">Strategy, design, technology and brand thinking — brought together under one roof.</p>
        <div data-a="up" style="--d:.6s"><a class="btn btn-sm" href="#work">Explore our work <x-icon name="arrow-circle" size="16" /></a></div>
    </div>
    <ul class="feature-list">
        @foreach($features as [$icon, $title, $text, $on])
            <li class="{{ $on ? 'on' : '' }}">
                <span class="f-icon"><x-icon :name="$icon" size="22" /></span>
                <div><h3>{{ $title }}</h3><p>{{ $text }}</p></div>
                <x-icon name="chevron" />
            </li>
        @endforeach
    </ul>
</section>
