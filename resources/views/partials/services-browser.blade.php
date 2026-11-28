{{-- Service categories as tabs with cards. Used on the Services page and on the home page. Optional: $heading, $anchor --}}
<section class="block wk2 sv2" data-sv2 data-src="{{ url('/_/services') }}" data-v="{{ \App\Support\SiteVersion::get() }}" @isset($anchor) id="{{ $anchor }}" @endisset>
    @isset($heading)
        <div class="wk2-head">
            <h2>{{ $heading }}</h2>
            @isset($sub)<p class="sv2-sub">{{ $sub }}</p>@endisset
        </div>
    @endisset
    <div class="wk2-tabs" role="tablist" aria-label="Service categories">
        <button type="button" role="tab" class="on" data-tag="all" aria-selected="true">All</button>
        @foreach($services as $s)
            <button type="button" role="tab" data-tag="{{ $s->slug }}" aria-selected="false">{{ $s->title }}</button>
        @endforeach
    </div>

    <div class="wk2-grid sv2-grid" id="sv2Grid">
        {{-- "All" tab: one card per category --}}
        @foreach($services as $s)
            <a href="{{ url('/services/' . $s->slug) }}" class="sv2-card is-cat" data-tag="all" hidden>
                <span class="sv2-ico"><x-icon :name="$s->icon ?: 'rocket'" size="20" /></span>
                <span class="sv2-cap">
                    <small>{{ $s->items_count }} services</small>
                    <b>{{ $s->title }}</b>
                    <em>{{ $s->short_description }}</em>
                </span>
                <span class="wk2-go"><x-icon name="arrow-right" size="16" /></span>
            </a>
        @endforeach
    </div>
</section>
