@extends('admin.layout')
@section('title', $item->name)
@section('content')
@php
    $sent = $item->created_at->timezone('Asia/Dhaka');
    $steps = ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'];
    $isSpam = $item->status === 'spam';
    $reached = array_search($item->status, array_keys($steps), true); // false for spam
    $promise = 30; // minutes; the Contact page promises a reply within 30 minutes
    $waited = (int) $item->created_at->diffInMinutes(now());
    $wa = 'https://wa.me/88' . $item->phone . '?text=' . rawurlencode('Hi ' . $item->name . ', this is Reganto IT Solution about the project brief you sent us.');
    $phoneShown = strlen($item->phone) === 11 ? substr($item->phone, 0, 5) . ' ' . substr($item->phone, 5) : $item->phone;
    $ago = function (int $m) {
        if ($m < 1) return 'less than a minute';
        if ($m < 60) return $m . ' min';
        if ($m < 1440) return intdiv($m, 60) . ' h ' . ($m % 60) . ' min';
        return intdiv($m, 1440) . ($m < 2880 ? ' day' : ' days');
    };
@endphp

<nav class="rq-nav" aria-label="Requests">
    <a class="rq-back" href="{{ route('admin.requests.index', $isSpam ? ['status' => 'spam'] : []) }}">
        <svg viewBox="0 0 20 20" width="16" height="16" aria-hidden="true"><path d="M12.5 4.5 7 10l5.5 5.5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        {{ $isSpam ? 'Spam' : 'All requests' }}
    </a>
    <span class="rq-pager">
        @if($newer)<a href="{{ route('admin.requests.show', $newer) }}" rel="prev">Newer</a>@else<span>Newer</span>@endif
        @if($older)<a href="{{ route('admin.requests.show', $older) }}" rel="next">Older</a>@else<span>Older</span>@endif
    </span>
</nav>

@if($isSpam)
    <div class="rq-spam" role="note">
        <p><b>Kept as spam.</b> The hidden anti-bot field on the form was filled in. Bots do that, but so can a browser's autofill. If this looks like a real person, move it to New.</p>
        <form method="POST" action="{{ route('admin.requests.update', $item) }}">@csrf @method('PUT')
            <button type="submit" name="status" value="new">Move to New</button>
        </form>
    </div>
@endif

<article class="rq">
    <section class="rq-main">
        <header class="rq-head">
            @if($item->company)<p class="rq-company">{{ $item->company }}</p>@endif
            <p class="rq-when">Sent <time datetime="{{ $item->created_at->toIso8601String() }}">{{ $sent->format('l, j F Y \a\t g:i A') }}</time> Dhaka time</p>
        </header>

        <dl class="rq-facts">
            <div class="rq-need">
                <dt>What they need</dt>
                <dd>
                    @forelse($item->services ?? [] as $s)
                        <span class="rq-chip">{{ $s }}</span>
                    @empty
                        <span class="rq-none">Not picked</span>
                    @endforelse
                </dd>
            </div>
            <div><dt>Budget</dt><dd>{!! $item->budget ? e($item->budget) : '<span class="rq-none">Not picked</span>' !!}</dd></div>
            <div><dt>Timeline</dt><dd>{!! $item->timeline ? e($item->timeline) : '<span class="rq-none">Not picked</span>' !!}</dd></div>
        </dl>

        <figure class="rq-letter">
            <figcaption>In their words</figcaption>
            @if(filled($item->message))
                <blockquote>{{ $item->message }}</blockquote>
            @else
                <p class="rq-none">They didn't write about the project. Ask what it does and who it is for when you call.</p>
            @endif
        </figure>
    </section>

    <aside class="rq-rail">
        @if($item->status === 'new')
            @php $state = $waited < 15 ? 'ok' : ($waited < $promise ? 'warn' : 'late'); @endphp
            <div class="rq-clock {{ $state }}" data-since="{{ $item->created_at->timestamp }}" data-promise="{{ $promise }}">
                <p class="rq-clock-k">Waiting for a reply</p>
                <p class="rq-clock-v" data-clock-text>{{ $ago($waited) }}</p>
                <div class="rq-bar" aria-hidden="true"><i style="--p:{{ min(1, $waited / $promise) }}"></i></div>
                <p class="rq-clock-note" data-clock-note>
                    @if($waited < $promise)
                        {{ $promise - $waited }} min left of the 30-minute reply promise.
                    @else
                        The 30-minute reply promise has passed. Call now.
                    @endif
                </p>
            </div>
        @elseif(!$isSpam)
            <div class="rq-clock done">
                <p class="rq-clock-k">{{ $item->status === 'closed' ? 'Closed' : 'Contacted' }}</p>
                <p class="rq-clock-v">{{ $item->updated_at->timezone('Asia/Dhaka')->format('j M, g:i A') }}</p>
                <p class="rq-clock-note">Marked {{ $ago((int) $item->updated_at->diffInMinutes(now())) }} ago.</p>
            </div>
        @endif

        <div class="rq-reach">
            <p class="rq-phone"><a href="tel:{{ $item->phone }}">{{ $phoneShown }}</a>
                <button type="button" class="rq-copy" data-copy="{{ $item->phone }}" aria-label="Copy phone number">Copy</button></p>
            <div class="rq-acts">
                <a class="rq-call" href="tel:{{ $item->phone }}">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1z" fill="currentColor"/></svg>
                    Call
                </a>
                <a class="rq-wa" href="{{ $wa }}" target="_blank" rel="noreferrer">
                    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm5.2 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7a11.5 11.5 0 0 1-4.6-4.1c-.4-.5-1-1.6-1-2.6 0-1.1.6-1.6.8-1.9.2-.2.5-.3.6-.3h.5c.2 0 .4 0 .6.5l.8 1.9c.1.1.1.3 0 .5l-.3.5-.4.4c-.1.2-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.1 1 2.1 1.3 2.4 1.5.3.1.5.1.6-.1l.9-1c.2-.3.4-.2.6-.1l1.8.9c.3.1.5.2.5.3.1.1.1.7-.1 1.3z" fill="currentColor"/></svg>
                    WhatsApp
                </a>
            </div>
            @if($item->email)
                <p class="rq-mail"><a href="mailto:{{ $item->email }}?subject={{ rawurlencode('Your project brief – Reganto IT Solution') }}">{{ $item->email }}</a></p>
            @endif
        </div>

        <form class="rq-steps" method="POST" action="{{ route('admin.requests.update', $item) }}">@csrf @method('PUT')
            <p class="rq-steps-k">Progress</p>
            <ol>
                @foreach($steps as $key => $label)
                    @php $i = $loop->index; $cls = $reached === false ? '' : ($i < $reached ? 'past' : ($i === $reached ? 'now' : '')); @endphp
                    <li class="{{ $cls }}">
                        <button type="submit" name="status" value="{{ $key }}" @if($cls === 'now') aria-current="step" disabled @endif>
                            <span class="rq-dot" aria-hidden="true"></span>
                            <span class="rq-step-t">{{ $label }}</span>
                            @if($cls === 'now')<span class="rq-step-s">Now</span>@elseif($cls === '')<span class="rq-step-s">Mark as {{ strtolower($label) }}</span>@endif
                        </button>
                    </li>
                @endforeach
            </ol>
        </form>

        <div class="rq-quiet">
            @unless($isSpam)
                <form method="POST" action="{{ route('admin.requests.update', $item) }}">@csrf @method('PUT')
                    <button type="submit" name="status" value="spam">Mark as spam</button>
                </form>
            @endunless
            <form method="POST" action="{{ route('admin.requests.destroy', $item) }}" data-confirm-title="Delete this request?" data-confirm="The brief from {{ $item->name }} and their phone number will be gone for good. To keep it but take it off the list, mark it Closed instead.">@csrf @method('DELETE')
                <button type="submit" class="rq-del">Delete request</button>
            </form>
        </div>
    </aside>
</article>

<script>
(function () {
  // Copy the phone number.
  document.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var done = function () { b.textContent = 'Copied'; setTimeout(function () { b.textContent = 'Copy'; }, 1600); };
      if (navigator.clipboard) navigator.clipboard.writeText(b.dataset.copy).then(done, function () {});
    });
  });
  // Keep the reply clock running while the page is open.
  var c = document.querySelector('[data-since]');
  if (!c) return;
  var since = +c.dataset.since * 1000, promise = +c.dataset.promise;
  var text = c.querySelector('[data-clock-text]'), note = c.querySelector('[data-clock-note]'), bar = c.querySelector('.rq-bar i');
  function ago(m) {
    if (m < 1) return 'less than a minute';
    if (m < 60) return m + ' min';
    if (m < 1440) return Math.floor(m / 60) + ' h ' + (m % 60) + ' min';
    return Math.floor(m / 1440) + (m < 2880 ? ' day' : ' days');
  }
  function tick() {
    var m = Math.max(0, Math.floor((Date.now() - since) / 60000));
    text.textContent = ago(m);
    bar.style.setProperty('--p', Math.min(1, m / promise));
    note.textContent = m < promise ? (promise - m) + ' min left of the 30-minute reply promise.' : 'The 30-minute reply promise has passed. Call now.';
    c.classList.remove('ok', 'warn', 'late');
    c.classList.add(m < 15 ? 'ok' : m < promise ? 'warn' : 'late');
  }
  setInterval(tick, 30000);
})();
</script>
@endsection
