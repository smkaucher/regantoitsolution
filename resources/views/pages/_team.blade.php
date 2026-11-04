<div class="pg-team">
    @foreach($team as $m)
        <article>
            @if($m->photo)<img src="{{ \App\Support\Media::url($m->photo) }}" alt="{{ $m->name }}" loading="lazy">
            @else<div class="pg-avatar">{{ mb_substr($m->name, 0, 1) }}</div>@endif
            <h3>{{ $m->name }}</h3><small>{{ $m->designation }}</small>
            @if($m->bio)<p>{{ $m->bio }}</p>@endif
            <div class="pg-links">
                @if($m->portfolio_url)<a href="{{ $m->portfolio_url }}" target="_blank" rel="noreferrer">Portfolio ↗</a>@endif
                @if($m->linkedin_url)<a href="{{ $m->linkedin_url }}" target="_blank" rel="noreferrer">LinkedIn</a>@endif
                @if($m->facebook_url)<a href="{{ $m->facebook_url }}" target="_blank" rel="noreferrer">Facebook</a>@endif
                @if($m->github_url)<a href="{{ $m->github_url }}" target="_blank" rel="noreferrer">GitHub</a>@endif
            </div>
        </article>
    @endforeach
</div>
