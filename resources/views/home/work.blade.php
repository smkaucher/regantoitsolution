@php
    // tab label => project tag it filters on
    $workTabs = ['All' => 'All', 'Web Development' => 'Web Development', 'Software' => 'Software', 'UI/UX Design' => 'UI/UX Design', 'Branding' => 'Graphics & Branding'];
    // Card order per tab (slugs). Anything not listed keeps its normal order after these.
    $workOrder = [
        'All' => ['regantify-platform-landing', 'rareman-fashion-store', 'every-step-breathes', 'shopnet-multi-category-store', 'shopnet-mobile-home-ui', 'storepal-e-commerce-store', 'made-to-conquer-rain', 'rareman-mobile-ui', 'shopnet-ai-chat-ui', 'walk-with-authority', 'shopnet-mobile-categories-ui'],
        'Web Development' => ['regantify-platform-landing', 'rareman-fashion-store', 'shopnet-multi-category-store', 'storepal-e-commerce-store'],
        'Software' => ['storepal-e-commerce-store', 'regantify-platform-landing', 'shopnet-multi-category-store', 'rareman-fashion-store'],
        'UI/UX Design' => ['shopnet-ai-chat-ui', 'storepal-e-commerce-store', 'rareman-mobile-ui', 'regantify-platform-landing', 'shopnet-mobile-categories-ui', 'rareman-fashion-store', 'shopnet-mobile-home-ui', 'shopnet-multi-category-store'],
    ];
    // Projects the admin marks "Featured" that aren't in the hand-picked lists above (e.g. a newly added one) go
    // first in every tab they belong to, so the admin's Featured switch decides what shows up front.
    $picked = collect($workOrder)->flatten()->unique();
    $newFeatured = $projects->filter(fn ($p) => $p->is_featured && $p->cover_image && !$picked->contains($p->slug))
        ->sortBy('sort_order')->pluck('slug')->all();
    foreach ($workTabs as $tag) {
        $workOrder[$tag] = array_values(array_unique(array_merge($newFeatured, $workOrder[$tag] ?? [])));
    }
@endphp
<section class="block wk2 hm hm-work" data-wk2 data-order="{{ json_encode($workOrder) }}" data-replay data-threshold="0.12">
    <div class="hm-head">
        <p class="hm-eb"><i></i>[ OUR WORK ]</p>
        <h2 class="hm-h2">Recent work that <em>drives results.</em></h2>
    </div>

    <div class="wk2-tabs" role="tablist">
        @foreach($workTabs as $label => $tag)
            <button type="button" role="tab" class="{{ $loop->first ? 'on' : '' }}" data-tag="{{ $tag }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Bento grid: 12 columns x 3 rows; site.js shows up to 7 cards and sets data-cards / data-slot for the layout --}}
    <div class="wk2-grid hw-bento" id="wk2Grid" data-limit="7">
        @foreach($projects as $p)
            @php
                $cover = \App\Support\Media::url($p->cover_image);
                $portrait = in_array($p->category, ['Graphics Design'], true);
            @endphp
            @continue(!$cover)
            <a href="{{ url('/projects?open=' . $p->slug) }}" class="wk2-card {{ $portrait ? 'is-portrait' : '' }}" data-slug="{{ $p->slug }}" data-i="{{ $loop->index }}" data-tags='@json($p->tags())' hidden>
                <span class="wk2-shot"><img src="{{ $cover }}" alt="{{ $p->title }}" loading="lazy" draggable="false"></span>
                <span class="wk2-cap">
                    <small>{{ $p->category }}</small>
                    <b>{{ $p->title }}</b>
                </span>
                <span class="wk2-go"><x-icon name="arrow-right" size="16" /></span>
            </a>
        @endforeach
    </div>
    <p class="wk2-more"><a class="btn btn-sm" href="{{ url('/projects') }}">View all projects <x-icon name="arrow-circle" size="16" /></a></p>
</section>
