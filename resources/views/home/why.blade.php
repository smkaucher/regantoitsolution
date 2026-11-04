<section class="block why" data-replay data-threshold="0.25">
    <div>
        <h2 data-a="left" style="--d:0s">Why Businesses Choose {{ $site['site_name'] ?? 'Reganto IT Solution' }}</h2>
        <p class="sub left" data-a="left" style="--d:.12s">A reliable partner that blends strong design thinking, clean engineering, and a clear delivery process. From discovery to launch — and beyond — we stay accountable.</p>
    </div>
    <ul>
        @foreach(['Strategy-Led UI/UX That Converts', 'Custom Builds Tailored to Your Goals', 'Transparent Communication & Process', 'Scalable Frontend & Backend Engineering', 'Performance, Speed & Quality Standards', 'Reliable Support & Long-Term Partnership'] as $i => $text)
            <li data-a="right" style="--d:{{ 0.25 + $i * 0.18 }}s">
                <span class="why-check" data-a="spin" style="--d:{{ 0.35 + $i * 0.18 }}s"><x-icon name="check" size="16" /></span>
                <span class="why-text">
                    @foreach(explode(' ', $text) as $k => $word)
                        <span class="why-word" data-a="word" style="--d:{{ 0.45 + $i * 0.18 + $k * 0.05 }}s">{{ $word }}</span>
                    @endforeach
                </span>
            </li>
        @endforeach
    </ul>
</section>
