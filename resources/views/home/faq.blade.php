@if($faqs->count())
{{-- FAQ: heading and a contact prompt on the left (sticky), questions on the right. On scroll-in the questions slide
     in from the right one by one; an open question gets a teal bar that grows down its left edge. --}}
<section class="block faq hm hm-faq" data-replay data-threshold="0.15">
    <div class="hf-side">
        <div class="hm-head">
            <p class="hm-eb"><i></i>[ FAQ ]</p>
            <h2 class="hm-h2">Questions, <em>answered.</em></h2>
            <p class="hm-sub">Can't find what you need? Ask us directly.</p>
        </div>
        <a class="hm-btn" href="{{ url('/contact') }}">Ask a question <span><x-icon name="arrow-right" size="14" /></span></a>
    </div>
    <div class="hf-list">
        @foreach($faqs as $i => $f)
            <details style="--k:{{ $i }}">
                <summary>
                    <span class="faq-q">{{ $f->question }}</span>
                    <span class="hf-plus" aria-hidden="true"></span>
                </summary>
                <p>{{ $f->answer }}</p>
            </details>
        @endforeach
    </div>
</section>
@endif
