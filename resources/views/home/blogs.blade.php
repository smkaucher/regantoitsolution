@if($blogs->count())
<section class="block" id="blogs" data-replay data-threshold="0.3">
    <h2 data-words data-stagger="0.05">Insights, Guides &amp; Trends From the Team</h2>
    <div class="posts">
        @foreach($blogs as $i => $b)
            <a href="{{ url('/blog/' . $b->slug) }}" data-a="up" style="--d:{{ 0.25 + $i * 0.15 }}s">
                @if($b->image)<img src="{{ \App\Support\Media::url($b->image) }}" alt="" loading="lazy">@endif
                <small>{{ optional($b->published_at)->format('M j, Y') }}{{ $b->category ? ' · ' . $b->category : '' }}</small>
                <h3>{{ $b->title }}</h3>
            </a>
        @endforeach
    </div>
    <p class="center" data-a="up" style="--d:.7s"><a class="btn btn-sm" href="{{ url('/blog') }}">View Articles</a></p>
</section>
@endif
