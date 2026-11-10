<div class="pg-team">
    @foreach($team as $m)
        @php
            $skills = array_values(array_filter(array_map('trim', explode(',', (string) ($m['skills'] ?? '')))));
            $links = array_filter([
                'Portfolio' => $m['portfolio_url'] ?? null, 'LinkedIn' => $m['linkedin_url'] ?? null,
                'Facebook' => $m['facebook_url'] ?? null, 'GitHub' => $m['github_url'] ?? null,
            ]);
        @endphp
        <article class="tm-card" tabindex="0" style="--i:{{ $loop->index }}@if(!empty($cover));--cover:url('{{ asset($cover) }}')@endif">
            <div class="tm-main">
                <div class="tm-photo">
                    @if(!empty($m['photo']))<img src="{{ asset($m['photo']) }}" alt="{{ $m['name'] }}" loading="lazy">
                    @else<div class="pg-avatar">{{ mb_substr($m['name'], 0, 1) }}</div>@endif
                </div>
                <h3>{{ $m['name'] }}</h3>
                <small class="tm-role">{{ $m['designation'] }}</small>
                <div class="tm-company">{{ $site['site_name'] ?? 'Reganto IT Solution' }}</div>
                @if(!empty($m['experience']))<div class="tm-badge">{{ $m['experience'] }} experience</div>@endif
                @if($skills)<div class="tm-skills">@foreach($skills as $sk)<i>{{ $sk }}</i>@endforeach</div>@endif
            </div>
            <div class="tm-hover">
                <b>{{ $m['name'] }}</b><small>{{ $m['designation'] }}</small>
                <p>{{ $m['bio'] ?? 'Part of the '.($site['site_name'] ?? 'Reganto IT Solution').' team.' }}</p>
                @if(!empty($m['experience']))<div class="tm-exp"><span>Experience</span>{{ $m['experience'] }}</div>@endif
                @if($links)
                    <div class="tm-links">
                        @foreach($links as $label => $url)<a href="{{ $url }}" target="_blank" rel="noreferrer">{{ $label }} ↗</a>@endforeach
                    </div>
                @else
                    <div class="tm-links"><span class="tm-nolink">Links coming soon</span></div>
                @endif
            </div>
        </article>
    @endforeach
</div>
