<section class="block svc2-sec" id="services">
    <div data-replay data-threshold="0.5">
        @php $w = 0; @endphp
        <h2 aria-label="UI/UX Design, Development &amp; Branding Services">
            @foreach(['UI/UX Design, Development', '& Branding Services'] as $line)
                <span class="svc2-line" aria-hidden="true">
                    @foreach(explode(' ', $line) as $word)
                        <span class="why-word" data-a="word" style="--d:{{ $w++ * 0.05 }}s">{{ $word }}</span>
                    @endforeach
                </span>
            @endforeach
        </h2>
        <p class="svc2-intro" data-a="up" style="--d:.35s">Whether you’re improving an existing platform or launching a new product, we combine thoughtful UX with premium UI and production-ready code. We deliver clean systems, fast pages, and scalable foundations that support long-term growth.</p>
    </div>
    <div class="svc2" data-tabs>
        <div class="svc2-tabs" role="tablist">
            @foreach($services as $i => $s)
                <button type="button" role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" class="{{ $i === 0 ? 'on' : '' }}" data-tab="{{ $i }}">
                    <span class="svc2-ind"></span>
                    <b>{{ $s->title }}</b><small>{{ $s->items->count() }} {{ $s->items->count() === 1 ? 'Item' : 'Items' }}</small>
                </button>
            @endforeach
        </div>
        <div class="svc2-body" role="tabpanel">
            @foreach($services as $i => $s)
                <div class="svc2-grid" data-panel="{{ $i }}" @if($i !== 0) hidden @endif>
                    @foreach($s->items as $j => $item)
                        <article style="--d:{{ $j * 0.07 }}s">
                            <span>{{ $j + 1 }}</span>
                            <div>
                                <h4>{{ $item->title }}</h4>
                                <p>{{ $item->description }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
