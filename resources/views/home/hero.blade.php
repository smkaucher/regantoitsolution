@php
    $prompts = ['What services do you offer?', 'Show me your recent projects', 'How can I book a service?', 'Where is your office?'];

    // Laptop screen: the 4 Software and the first 4 Branding projects from "Recent Work", in a random order.
    $projects = \App\Models\Project::active()->get();
    $softwareOrder = ['storepal-e-commerce-store', 'regantify-platform-landing', 'shopnet-multi-category-store', 'rareman-fashion-store'];
    $software = $projects->filter(fn ($p) => in_array('Software', $p->tags(), true))
        ->sortBy(fn ($p) => ($i = array_search($p->slug, $softwareOrder, true)) === false ? 99 : $i)->take(4);
    $branding = $projects->filter(fn ($p) => in_array('Graphics & Branding', $p->tags(), true))->take(4);
    $shots = $software->concat($branding)
        ->map(fn ($p) => ['title' => $p->title, 'src' => \App\Support\Media::url($p->cover_image), 'tall' => !in_array('Software', $p->tags(), true)])
        ->filter(fn ($s) => $s['src'])->shuffle()->values();

    $bg = asset('assets/img/hero-bg.webp') . '?v=' . filemtime(public_path('assets/img/hero-bg.webp'));
@endphp
<section class="hero hero--photo" id="product" data-replay data-threshold="0.25">
    {{--
        Background photo (1808 x 751). The laptop and phone screens are white in the photo; home.js lays the
        desktop and the logo onto them with a perspective transform, from the corner points in data-quad
        (fractions of the photo: top-left, top-right, bottom-right, bottom-left). The phone stands clear of the
        laptop screen, so no cut-out copy of the photo is needed to keep it in front.
    --}}
    <div class="hero-bg" aria-hidden="true">
        <div class="hero-stage">
            <img class="hero-photo-img" src="{{ $bg }}" alt="" width="1808" height="751" fetchpriority="high" decoding="async">

            {{-- Laptop screen: a Windows desktop whose wallpaper is the moving work images, desktop icons down the left, and the taskbar. --}}
            <div class="hero-quad hero-screen" data-quad=".72339,.43142 .87224,.41478 .85177,.71305 .70243,.70106">
                <div class="win-wall">
                    <div class="hero-shots">
                        @for($c = 0; $c < 2; $c++)
                            @foreach($shots as $s)
                                <figure class="hero-shot {{ $s['tall'] ? 'is-tall' : '' }}">
                                    <img src="{{ $s['src'] }}" alt="" @if($c > 0 || $loop->index > 2) loading="lazy" @endif decoding="async" draggable="false">
                                </figure>
                            @endforeach
                        @endfor
                    </div>
                </div>
                <div class="win-icons">
                    <span class="win-dico"><i class="win-app-ico pc"></i>This PC</span>
                    <span class="win-dico"><i class="win-app-ico files"></i>Projects</span>
                    <span class="win-dico"><i class="win-logo"><img src="{{ asset('assets/img/logo.png') }}" alt="" width="300" height="100" decoding="async"></i>Reganto</span>
                    <span class="win-dico"><i class="win-app-ico web"></i>Browser</span>
                    <span class="win-dico"><i class="win-app-ico bin"></i>Recycle Bin</span>
                </div>
                <div class="win-task">
                    <span class="win-apps">
                        <i class="win-start"><b></b><b></b><b></b><b></b></i>
                        <i class="win-search"></i>
                        <i class="win-app-ico files"></i>
                        <i class="win-app-ico web on"></i>
                        <i class="win-app-ico store"></i>
                        <i class="win-app-ico mail"></i>
                    </span>
                    <span class="win-tray"><i class="win-wifi"></i><span class="win-clock" data-win-clock><b>10:00</b><small>01/01/2026</small></span></span>
                </div>
            </div>

            <div class="hero-quad hero-phone-screen" data-quad=".87511,.50799 .92152,.50665 .90625,.78709 .85796,.78109">
                <img src="{{ asset('assets/img/logo.png') }}" alt="" width="300" height="100" decoding="async">
            </div>
        </div>
    </div>

    <div class="hero-copy">
        <span class="eyebrow" data-a="up" style="--d:.05s">DIGITAL STUDIO · {{ date('Y') }}</span>
        <h1>
            <span class="hl"><span data-a="line" style="--d:.17s">One studio.</span></span>
            <span class="hl"><span data-a="line" style="--d:.29s">Every <em class="shine">digital</em></span></span>
            <span class="hl"><span data-a="line" style="--d:.41s">possibility.</span></span>
        </h1>
        <p data-a="up" style="--d:.53s">{{ $site['hero_subtitle'] ?? 'Software, UI/UX design, branding, marketing and cyber security for startups and growing companies.' }}</p>
        <div class="hero-cta" data-a="pop" style="--d:.65s">
            <a class="btn" href="{{ url('/contact') }}">Start a project <x-icon name="arrow-circle" /></a>
            <a class="btn ghost" href="#work">Explore our work <span class="bob">↓</span></a>
        </div>
        <button type="button" class="aib" data-ai-open data-a="up" style="--d:.77s" aria-label="Open Reganto AI chat">
            <span class="aib-field">
                <span class="ai-typed aib-typed" data-typing='@json($prompts)' data-caret="ai-caret"></span>
            </span>
        </button>
        @php
            // number, suffix, decimals, label
            $stats = [[80, '+', 0, 'Projects Delivered'], [40, '+', 0, 'Happy Clients'], [4.9, '★', 1, 'Client Rating'], [24, '/7', 0, 'Digital Support']];
        @endphp
        <div class="hero-stats" data-a="up" style="--d:.9s">
            @foreach($stats as [$value, $suffix, $decimals, $label])
                <div class="hero-stat">
                    <strong><span data-count="{{ $value }}" data-suffix="{{ $suffix }}" data-decimals="{{ $decimals }}">0</span></strong>
                    {{ $label }}
                </div>
            @endforeach
        </div>
    </div>
</section>
