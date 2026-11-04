@if($faqs->count())
<section class="block faq" id="faq" data-replay data-threshold="0.2">
    <h2 data-a="up" style="--d:0s">Frequently Asked Questions</h2>
    @foreach($faqs as $i => $f)
        <details data-a="up" style="--d:{{ 0.07 + $i * 0.07 }}s">
            <summary>
                <span class="faq-n" data-a="left" style="--d:{{ 0.2 + $i * 0.07 }}s">{{ sprintf('%02d', $i + 1) }}</span>
                <span class="faq-q" data-a="up" style="--d:{{ 0.25 + $i * 0.07 }}s">{{ $f->question }}</span>
                <span class="faq-chev" data-a="left" style="--d:{{ 0.3 + $i * 0.07 }}s"><x-icon name="chevron" /></span>
            </summary>
            <p>{{ $f->answer }}</p>
        </details>
    @endforeach
</section>
@endif
