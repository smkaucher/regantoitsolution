@extends('layouts.app')
@section('title', 'Start a Project – ' . ($site['site_name'] ?? 'Reganto IT Solution'))
@section('description', 'Tell us what you want to build. Send a project brief, or reach us on WhatsApp, email or phone. We reply within 30 minutes.')
@section('content')
@php
    use App\Models\ProjectRequest;
    $addr = $site['address'] ?: config('site.address');
    $mail = $site['email'] ?? null;
    $tel = !empty($site['phone']) ? preg_replace('/[^\d+]/', '', $site['phone']) : null;
    $wa = $site['whatsapp'] ?? null;
    $sent = session('brief_sent');
    $oldServices = old('services', []);
@endphp
{{--
    Contact: a "Start a project" brief on the left (saved to Admin > Project requests, ContactController@store;
    the phone must be an 11-digit Bangladeshi number), direct contact details and the next steps on the right.
--}}
<section class="ct hm" id="brief">
    <div class="ct-head">
        <p class="ct-crumb"><a href="{{ url('/') }}">Home</a> <span>→</span> Contact</p>
        <h1 class="ct-title">Start a <em>project.</em></h1>
        <p class="ct-lead">Tell us what you want to build. We read every brief and reply within 30 minutes.</p>
    </div>

    <div class="ct-grid">
        @if($sent)
            <div class="ct-done" role="status">
                <span class="ct-done-ico"><x-icon name="check" size="26" /></span>
                <h2>Brief received.</h2>
                <p>Thank you. We'll call you on the number you gave us within 30 minutes during working hours.</p>
                <a class="hm-btn" href="{{ url('/') }}">Back to home <span><x-icon name="arrow-right" size="14" /></span></a>
            </div>
        @else
            <form class="ct-form" method="POST" action="{{ route('contact.store') }}" novalidate>
                @csrf
                {{-- Honeypot: hidden from people, filled by bots --}}
                {{-- "hidden" (not just moved off screen) so browser autofill and password managers never fill it --}}
                <div class="ct-hp" aria-hidden="true" hidden><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                @if($errors->any())<p class="ct-err-top" role="alert">Please check the highlighted fields.</p>@endif

                <div class="ct-row">
                    <label class="ct-field @error('name') bad @enderror"><span>Your name *</span>
                        <input type="text" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                        @error('name')<em>{{ $message }}</em>@enderror
                    </label>
                    <label class="ct-field @error('phone') bad @enderror"><span>Phone number *</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required inputmode="numeric" maxlength="20" pattern="01[3-9][0-9]{8}" placeholder="01XXXXXXXXX" autocomplete="tel" data-bd-phone>
                        <em class="ct-phone-msg" @unless($errors->has('phone')) hidden @endunless>{{ $errors->first('phone') ?: 'Enter an 11-digit Bangladeshi mobile number, like 01712345678.' }}</em>
                    </label>
                </div>
                <div class="ct-row">
                    <label class="ct-field @error('email') bad @enderror"><span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" maxlength="190" autocomplete="email">
                        @error('email')<em>{{ $message }}</em>@enderror
                    </label>
                    <label class="ct-field"><span>Company</span>
                        <input type="text" name="company" value="{{ old('company') }}" maxlength="160" autocomplete="organization">
                    </label>
                </div>

                <fieldset class="ct-chips">
                    <legend>What do you need?</legend>
                    @foreach($serviceTitles as $t)
                        <label><input type="checkbox" name="services[]" value="{{ $t }}" @checked(in_array($t, $oldServices, true))><span>{{ $t }}</span></label>
                    @endforeach
                </fieldset>

                <fieldset class="ct-chips">
                    <legend>Budget</legend>
                    @foreach(ProjectRequest::BUDGETS as $b)
                        <label><input type="radio" name="budget" value="{{ $b }}" @checked(old('budget') === $b)><span>{{ $b }}</span></label>
                    @endforeach
                </fieldset>

                <label class="ct-field ct-select"><span>Timeline</span>
                    <select name="timeline">
                        @foreach(ProjectRequest::TIMELINES as $tl)
                            <option value="{{ $tl }}" @selected(old('timeline', ProjectRequest::TIMELINES[0]) === $tl)>{{ $tl }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="ct-field"><span>Tell us about the project</span>
                    <textarea name="message" rows="5" maxlength="3000" placeholder="What it does, who it is for, and what already exists.">{{ old('message') }}</textarea>
                    @error('message')<em>{{ $message }}</em>@enderror
                </label>

                <button class="hm-btn" type="submit">Send the brief <span><x-icon name="arrow-right" size="14" /></span></button>
            </form>
        @endif

        <aside class="ct-side">
            @if($mail)
                <div class="ct-block"><p class="ct-k">Write directly</p><a class="ct-big" href="mailto:{{ $mail }}">{{ $mail }}</a></div>
            @endif
            <div class="ct-pair">
                @if($tel)<div><p class="ct-k">Call</p><a href="tel:{{ $tel }}">{{ $site['phone'] }}</a></div>@endif
                <div><p class="ct-k">Local time</p><b class="ct-clock" data-dhaka-clock>--:--</b><small>Dhaka</small></div>
                @if($wa)<div><p class="ct-k">WhatsApp</p><a href="{{ $wa }}" target="_blank" rel="noreferrer">Chat with us now ↗</a></div>@endif
                <div><p class="ct-k">Visit</p><span>{{ $addr }}</span></div>
            </div>
            <div class="ct-block">
                <p class="ct-k">What happens next</p>
                <ol class="ct-steps">
                    <li><b>We reply within 30 minutes</b><span>We read your brief and ask the questions that matter.</span></li>
                    <li><b>A free 15-minute strategy call</b><span>We talk through what you need and what to build first.</span></li>
                    <li><b>A clear quote</b><span>Scope, timeline and price in writing before any work starts.</span></li>
                </ol>
            </div>
        </aside>
    </div>
</section>

<section class="block">
    <div class="map-card">
        <div class="map-info">
            <span class="map-pin"><x-icon name="rocket" size="22" /></span>
            <h3>Visit our office</h3>
            <p>{{ $addr }}</p>
            <a class="btn btn-sm" href="{{ config('site.map_url') }}" target="_blank" rel="noreferrer">Get directions <x-icon name="arrow-circle" size="16" /></a>
        </div>
        <div class="map-frame">
            <iframe title="Reganto IT Solution on the map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen
                    src="https://maps.google.com/maps?q={{ rawurlencode(config('site.map_query')) }}&z=16&output=embed"></iframe>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
  // Phone: digits only, at most 11; show the hint when it is not a valid Bangladeshi number.
  var f = document.querySelector('.ct-form');
  if (f) {
    var ph = f.querySelector('[data-bd-phone]'), msg = f.querySelector('.ct-phone-msg'), ok = /^01[3-9]\d{8}$/;
    var check = function (strict) {
      var bad = !ok.test(ph.value);
      msg.hidden = !(bad && (strict || ph.value.length >= 11));
      ph.closest('.ct-field').classList.toggle('bad', !msg.hidden);
      return !bad;
    };
    // Bangla digits become 0-9, and a pasted +880 / 880 number becomes 01XXXXXXXXX, before the 11-digit limit.
    var tidy = function (v) {
      v = v.replace(/[০-৯]/g, function (c) { return String(c.charCodeAt(0) - 0x09E6); }).replace(/\D/g, '');
      if (v.indexOf('880') === 0 && v.length > 11) v = '0' + v.slice(3);
      return v.slice(0, 11);
    };
    ph.addEventListener('input', function () { ph.value = tidy(ph.value); check(false); });
    ph.addEventListener('blur', function () { if (ph.value) check(true); });
    f.addEventListener('submit', function (e) {
      var nm = f.querySelector('[name="name"]');
      var good = check(true);
      if (!nm.value.trim()) { nm.closest('.ct-field').classList.add('bad'); good = false; }
      if (!good) { e.preventDefault(); (nm.value.trim() ? ph : nm).focus(); }
    });
  }
  // Dhaka clock
  var c = document.querySelector('[data-dhaka-clock]');
  if (c) {
    var tick = function () { c.textContent = new Date().toLocaleTimeString('en-GB', { timeZone: 'Asia/Dhaka', hour: '2-digit', minute: '2-digit' }); };
    tick(); setInterval(tick, 15000);
  }
})();
</script>
@endpush
