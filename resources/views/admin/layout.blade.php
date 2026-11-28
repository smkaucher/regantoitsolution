<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Dashboard') – Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v={{ filemtime(public_path('assets/css/admin.css')) }}">
</head>
<body class="ad">
@php
    $nav = [
        ['Dashboard', 'admin.dashboard', 'admin'],
        ['Project requests', 'admin.requests.*', 'requests'],
        ['Site settings', 'admin.settings', 'settings'],
        ['Services', 'admin.services.*', 'services'],
        ['Projects', 'admin.projects.*', 'projects'],
        ['Team', 'admin.team.*', 'team'],
    ];
@endphp
<aside class="ad-side">
    <a class="ad-brand" href="{{ route('admin.dashboard') }}">Reganto <small>Admin</small></a>
    <nav>
        @foreach($nav as [$label, $route, $match])
            <a href="{{ route(str_ends_with($route, '.*') ? str_replace('.*', '.index', $route) : $route) }}" class="{{ request()->routeIs($route) ? 'on' : '' }}">{{ $label }}@if($match === 'requests' && ($newReq = \App\Models\ProjectRequest::where('status', 'new')->count())) <span class="ad-badge">{{ $newReq }}</span>@endif</a>
        @endforeach
        <span class="ad-group">Home page content</span>
        @foreach(\App\Support\ContentTypes::all() as $key => $c)
            <a href="{{ route('admin.content.index', $key) }}" class="{{ request()->route('type') === $key ? 'on' : '' }}">{{ $c['label'] }}@if($key === 'testimonials' && ($waitingReviews = \App\Models\Testimonial::waiting()->count())) <span class="ad-badge">{{ $waitingReviews }}</span>@endif</a>
        @endforeach
    </nav>
    <div class="ad-foot">
        <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'on' : '' }}">Profile</a>
        <a href="{{ url('/') }}" target="_blank" rel="noreferrer">View site ↗</a>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Log out</button></form>
    </div>
</aside>
<main class="ad-main">
    <header class="ad-top"><h1>@yield('title', 'Dashboard')</h1><a class="ad-me" href="{{ route('admin.profile') }}" title="Change your name, email or password">{{ auth()->user()->name }}</a></header>
    @if(session('ok'))<div class="ad-ok">{{ session('ok') }}</div>@endif
    @if(in_array(session('expired'), ['saved_nothing', 'restored'], true))
        <div class="ad-err">The session had timed out, so that save didn't go through. What you typed is back in the form below: check it and save again. Photos have to be picked again.</div>
    @endif
    @if(request()->boolean('upload_error'))<div class="ad-err">Nothing was saved: the photos were too large to send together. Up to {{ ini_get('post_max_size') }} and {{ ini_get('max_file_uploads') }} files can go in one save. Add fewer or smaller photos, save, then add the rest.</div>@endif
    @yield('content')
</main>

{{-- One "Are you sure?" dialog for the whole admin. Any form, button or checkbox with data-confirm="message" asks
     here first (optional data-confirm-title and data-confirm-ok for the heading and the red button's label). --}}
<dialog class="ad-confirm" id="adConfirm" aria-labelledby="adConfirmTitle" aria-describedby="adConfirmMsg">
    <div class="ad-confirm-box">
        <span class="ad-confirm-ico" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="22" height="22"><path d="M4 7h16M10 11v6M14 11v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4h6v3" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <h2 id="adConfirmTitle" data-c-title>Are you sure?</h2>
        <p id="adConfirmMsg" data-c-msg></p>
        <div class="ad-confirm-btns">
            <button type="button" class="ad-confirm-no" data-c-no>Cancel</button>
            <button type="button" class="ad-confirm-yes" data-c-ok>Delete</button>
        </div>
    </div>
</dialog>
<script>
// Keep the session alive while an admin page is open, so a long edit doesn't time out before it is saved.
setInterval(function () { fetch('{{ route('admin.ping') }}', { credentials: 'same-origin', cache: 'no-store' }).catch(function () {}); }, 10 * 60 * 1000);
(function () {
  var dlg = document.getElementById('adConfirm');
  if (!dlg || !dlg.showModal) return; // very old browsers: the action simply runs
  var title = dlg.querySelector('[data-c-title]'), msg = dlg.querySelector('[data-c-msg]');
  var ok = dlg.querySelector('[data-c-ok]'), no = dlg.querySelector('[data-c-no]'), pending = null;

  function ask(el, run) {
    title.textContent = el.dataset.confirmTitle || 'Are you sure?';
    msg.textContent = el.dataset.confirm;
    ok.textContent = el.dataset.confirmOk || 'Delete';
    pending = run;
    dlg.showModal();
    no.focus(); // the safe choice has focus, so Enter does not delete by accident
  }
  function cancel() { pending = null; if (dlg.open) dlg.close(); }

  ok.addEventListener('click', function () { var run = pending; pending = null; dlg.close(); if (run) run(); });
  no.addEventListener('click', cancel);
  dlg.addEventListener('cancel', function () { pending = null; }); // Esc
  dlg.addEventListener('click', function (e) { if (e.target === dlg) cancel(); }); // click on the dark backdrop

  // Forms (Delete buttons that post to the server)
  document.addEventListener('submit', function (e) {
    var f = e.target;
    if (!f.dataset.confirm || f.dataset.confirmed) return;
    e.preventDefault();
    var by = e.submitter;
    ask(f, function () {
      f.dataset.confirmed = '1';
      if (f.requestSubmit) f.requestSubmit(by || undefined); else f.submit();
    });
  }, true);

  // Photo uploads: check the server's limits before sending, so a too-big save never loses what was typed.
  var maxBytes = {{ (function ($v) { $n = (int) $v; $u = strtoupper(substr(trim($v), -1)); return $n * ($u === 'G' ? 1073741824 : ($u === 'M' ? 1048576 : ($u === 'K' ? 1024 : 1))); })(ini_get('post_max_size')) }};
  var maxFiles = {{ (int) ini_get('max_file_uploads') }};
  document.addEventListener('submit', function (e) {
    var f = e.target, bytes = 0, files = 0;
    if (f.enctype !== 'multipart/form-data') return;
    f.querySelectorAll('input[type=file]').forEach(function (i) { Array.prototype.forEach.call(i.files || [], function (x) { bytes += x.size; files++; }); });
    if (bytes < maxBytes * 0.95 && files <= maxFiles) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    var box = f.querySelector('.ad-err.ad-upload') || f.insertBefore(document.createElement('div'), f.firstChild);
    box.className = 'ad-err ad-upload';
    box.textContent = 'Too many photos for one save: ' + files + ' files, ' + (bytes / 1048576).toFixed(1) + ' MB. One save can take up to '
      + maxFiles + ' files and ' + Math.floor(maxBytes / 1048576) + ' MB. Remove some, save, then add the rest.';
    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }, true);

  // Buttons that act in the page (Remove an item row) and "Remove image" checkboxes
  document.addEventListener('click', function (e) {
    var el = e.target.closest && e.target.closest('button[data-confirm], input[data-confirm]');
    if (!el || el.dataset.confirmed) return;
    if (el.type === 'checkbox') {
      if (!el.checked) return; // un-ticking never needs asking
      e.preventDefault();      // keeps it un-ticked until confirmed
      ask(el, function () { el.checked = true; el.dispatchEvent(new Event('change', { bubbles: true })); });
      return;
    }
    e.preventDefault();
    e.stopImmediatePropagation();
    ask(el, function () { el.dataset.confirmed = '1'; el.click(); delete el.dataset.confirmed; });
  }, true);
})();
</script>
</body>
</html>
