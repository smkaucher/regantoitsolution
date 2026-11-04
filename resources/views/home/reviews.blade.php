@if($testimonials->count())
<section class="block">
    <h2>What Clients Say About {{ $site['site_name'] ?? 'Reganto IT Solution' }}</h2>
    <p class="sub">★ Rated 4.9 out of 5.0 · 30+ Reviews · <a href="https://clutch.co/"><u>Read our reviews on Clutch</u></a></p>
    <div class="cf" data-carousel role="region" aria-roledescription="carousel" aria-label="Client reviews" tabindex="0">
        <button type="button" class="cf-arrow prev" data-prev aria-label="Previous review"><x-icon name="chevron" size="18" style="transform:rotate(180deg)" /></button>
        <div class="cf-stage">
            @foreach($testimonials as $t)
                <figure class="cf-card" data-card>
                    <div class="cf-stars" aria-label="{{ $t->rating }} out of 5 stars">{{ str_repeat('★', $t->rating) }}</div>
                    <blockquote>“{{ $t->quote }}”</blockquote>
                    <figcaption>
                        <i class="cf-av">{{ mb_substr(trim($t->name), 0, 1) }}</i>
                        <span><b>{{ $t->name }}</b><small>{{ $t->role }}</small></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
        <button type="button" class="cf-arrow next" data-next aria-label="Next review"><x-icon name="chevron" size="18" /></button>
        <div class="cf-dots">
            @foreach($testimonials as $i => $t)
                <button type="button" data-dot="{{ $i }}" aria-label="Show review {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>
</section>
@endif
