@extends('layouts.app')
@section('title', 'About Us – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'One team for everything digital: websites, apps, design, marketing, SEO, cyber security and IT support. 360 degree service from idea to launch and beyond.')
@section('content')
    @php
        $name = $site['site_name'] ?? 'Reganto IT Solution';
        $areas = [
            ['layers', 'Web & App Development', 'Websites, web apps and mobile apps built fast and built to last.'],
            ['sparkle', 'UI/UX Design', 'Simple, beautiful screens that people love to use.'],
            ['hex', 'Graphics & Branding', 'Logos, posters, banners and a look that people remember.'],
            ['chart', 'Digital Marketing', 'Social media, ads and content that bring you customers.'],
            ['rocket', 'SEO', 'Show up on Google and get found by the right people.'],
            ['shield', 'Cyber Security', 'Keep your data, website and business safe from attacks.'],
            ['cloud', 'Cloud & Hosting', 'Fast, safe servers, domains and email set up for you.'],
            ['gear', 'IT Support', 'Fixes, updates and help whenever something goes wrong.'],
            ['ring', 'E-commerce', 'Online shops with easy payment, delivery and order tools.'],
            ['wave', 'Software & Automation', 'Custom tools that save your time and remove manual work.'],
        ];
        $steps = [
            ['01', 'Talk', 'Tell us your idea or problem. We listen first.'],
            ['02', 'Plan', 'We make a clear plan with a fair price and timeline.'],
            ['03', 'Build', 'We design and build it, and show you progress often.'],
            ['04', 'Launch & Support', 'We go live together, then we stay to help you grow.'],
        ];
    @endphp
    @include('pages._head', ['title' => 'About Us', 'crumb' => 'About Us', 'lead' => 'One team for everything digital, from the first idea to launch and beyond.'])

    <section class="block ab-intro" data-replay data-threshold="0.2">
        <div class="ab-copy">
            <span class="svc2-eyebrow" data-a="up">WHO WE ARE</span>
            <h2 data-a="up" style="--d:.1s">Design. Develop. <em>Grow.</em></h2>
            <p data-a="up" style="--d:.2s">{{ $name }} is a full-service digital studio. We give you a <b>360° service</b>: whatever your business needs in the digital world, our team can do it.</p>
            <p data-a="up" style="--d:.3s">We build websites and apps, design your brand, run your marketing and SEO, protect you from cyber threats, and support your IT every day. One team, one place, no hand-offs.</p>
            <a class="btn" href="{{ url('/contact') }}" data-a="up" style="--d:.4s">Start a project <x-icon name="arrow-circle" size="16" /></a>
        </div>
        <div class="ab-360" data-a="up" style="--d:.2s" aria-hidden="true">
            <i></i><i></i>
            <div><strong>360°</strong><span>Digital service</span></div>
        </div>
    </section>

    <section class="block ab-areas" data-replay data-threshold="0.1">
        <div class="ab-title">
            <span class="svc2-eyebrow" data-a="up">WHAT WE DO</span>
            <h2 data-a="up" style="--d:.1s">Everything you need, in one place</h2>
        </div>
        <div class="ab-grid">
            @foreach($areas as $i => [$icon, $title, $text])
                <article data-a="up" style="--d:{{ ($i % 5) * .07 }}s">
                    <span class="ab-ico"><x-icon :name="$icon" size="22" /></span>
                    <h3>{{ $title }}</h3>
                    <p>{{ $text }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="block ab-steps" data-replay data-threshold="0.15">
        <div class="ab-title">
            <span class="svc2-eyebrow" data-a="up">HOW WE WORK</span>
            <h2 data-a="up" style="--d:.1s">Simple steps, clear results</h2>
        </div>
        <ol>
            @foreach($steps as $i => [$n, $title, $text])
                <li data-a="up" style="--d:{{ $i * .1 }}s"><span>{{ $n }}</span><h3>{{ $title }}</h3><p>{{ $text }}</p></li>
            @endforeach
        </ol>
    </section>

    @if($stats->count())
    <section class="block"><div class="pg-stats">
        @foreach($stats as $s)<div><b>{{ rtrim(rtrim(number_format($s->value, 1), '0'), '.') }}{{ $s->suffix }}</b><span>{{ $s->label }}</span></div>@endforeach
    </div></section>
    @endif

    @if(count($team))
    <section class="block"><h2>Meet the team</h2>@include('pages._team', ['team' => $team])
        <p class="center"><a class="btn btn-sm" href="{{ url('/team') }}">Full team</a></p></section>
    @endif

    <section class="block ab-cta">
        <div>
            <h2>Have an idea? Let's talk.</h2>
            <p>Tell us what you need. We reply within 30 minutes.</p>
            <a class="btn" href="{{ url('/contact') }}">Contact us <x-icon name="arrow-circle" size="16" /></a>
        </div>
    </section>
@endsection
