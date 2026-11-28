/* Reganto IT Solution: home page, reviews 3D carousel. Re-run for sections that load later: initCarousels(root). */
(function () {
  'use strict';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function initCarousels(scope) {
    scope.querySelectorAll('[data-carousel]').forEach(function (root) {
      if (root.dataset.ready) return;
      root.dataset.ready = '1';
      var cards = root.querySelectorAll('[data-card]'), dots = root.querySelectorAll('[data-dot]');
      var n = cards.length, active = 0, paused = false, startX = null;
      if (!n) return;

      function render() {
        cards.forEach(function (card, i) {
          var d = (((i - active) % n) + n) % n;
          if (d > n / 2) d -= n;
          var a = Math.abs(d);
          card.style.setProperty('--d', d);
          card.style.setProperty('--a', a);
          card.style.setProperty('--s', Math.sign(d));
          card.style.zIndex = 10 - a;
          card.style.opacity = a === 0 ? 1 : a === 1 ? 0.65 : a === 2 ? 0.3 : 0;
          card.style.pointerEvents = a > 2 ? 'none' : 'auto';
          card.classList.toggle('on', d === 0);
          if (d === 0) card.removeAttribute('aria-hidden'); else card.setAttribute('aria-hidden', 'true');
        });
        dots.forEach(function (dot, i) { dot.classList.toggle('on', i === active); });
      }
      function go(i) { active = ((i % n) + n) % n; render(); }

      cards.forEach(function (card, i) { card.addEventListener('click', function () { if (i !== active) go(i); }); });
      dots.forEach(function (dot, i) { dot.addEventListener('click', function () { go(i); }); });
      var prev = root.querySelector('[data-prev]'), next = root.querySelector('[data-next]');
      if (prev) prev.addEventListener('click', function () { go(active - 1); });
      if (next) next.addEventListener('click', function () { go(active + 1); });

      root.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') go(active - 1);
        if (e.key === 'ArrowRight') go(active + 1);
      });
      ['mouseenter', 'focusin'].forEach(function (ev) { root.addEventListener(ev, function () { paused = true; }); });
      ['mouseleave', 'focusout'].forEach(function (ev) { root.addEventListener(ev, function () { paused = false; }); });
      root.addEventListener('pointerdown', function (e) { startX = e.clientX; });
      root.addEventListener('pointerup', function (e) {
        if (startX === null) return;
        var dx = e.clientX - startX; startX = null;
        if (Math.abs(dx) > 45) go(active + (dx < 0 ? 1 : -1));
      });

      render();
      if (!reduce) setInterval(function () { if (!paused && document.body.contains(root)) go(active + 1); }, 4500);
    });
  }

  window.initCarousels = initCarousels;
  initCarousels(document);
})();

/* Reviews "Write a review": opens the dialog and sends the form in the background. The review is saved hidden
   until an admin shows it. Errors from the server (422) are shown under their fields. Re-run for sections that
   load later: initReviewForms(root). */
(function () {
  'use strict';
  var t = function (k) { return window.t ? window.t(k) : k; };

  function cookie(name) {
    var m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : '';
  }

  function initReviewForms(scope) {
    scope.querySelectorAll('[data-review-dialog]').forEach(function (dlg) {
      if (dlg.dataset.ready) return;
      dlg.dataset.ready = '1';
      var sec = dlg.closest('section') || document;
      var form = dlg.querySelector('[data-review-form]');
      var status = form.querySelector('[data-review-status]');
      var send = form.querySelector('[type=submit]');
      var count = form.querySelector('[data-review-count]');
      var quote = form.elements.quote;

      function say(msg, kind) { status.textContent = msg || ''; status.className = 'rv-status' + (kind ? ' ' + kind : ''); }
      function clearErrors() {
        form.querySelectorAll('[data-err]').forEach(function (e) { e.textContent = ''; });
        form.querySelectorAll('.has-err').forEach(function (e) { e.classList.remove('has-err'); });
      }
      function showErrors(errors) {
        Object.keys(errors).forEach(function (k) {
          var e = form.querySelector('[data-err="' + k + '"]');
          if (!e) return;
          e.textContent = [].concat(errors[k])[0];
          e.parentNode.classList.add('has-err');
        });
        var first = form.querySelector('.has-err input, .has-err textarea');
        if (first) first.focus();
      }
      function open() {
        form.classList.remove('sent');
        say('');
        clearErrors();
        if (dlg.showModal) dlg.showModal(); else dlg.setAttribute('open', '');
        form.elements.name.focus();
      }
      function close() { if (dlg.close) dlg.close(); else dlg.removeAttribute('open'); }

      sec.querySelectorAll('[data-review-open]').forEach(function (b) { b.addEventListener('click', open); });
      dlg.querySelectorAll('[data-review-close]').forEach(function (b) { b.addEventListener('click', close); });
      // A click on the dark backdrop lands on the dialog itself (the form covers the rest).
      dlg.addEventListener('click', function (e) { if (e.target === dlg) close(); });
      quote.addEventListener('input', function () { count.textContent = quote.value.length; });

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();
        var errs = {}, text = quote.value.trim(), file = form.elements.photo.files[0];
        if (!form.elements.name.value.trim()) errs.name = t('Please enter your name.');
        if (!text) errs.quote = t('Please write your review.');
        else if (text.length < 10) errs.quote = t('Please write a little more (at least 10 letters).');
        if (file && file.size > 2 * 1024 * 1024) errs.photo = t('The photo must be smaller than 2 MB.');
        if (Object.keys(errs).length) { showErrors(errs); return; }

        send.disabled = true;
        say(t('Sending…'));
        fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          credentials: 'same-origin',
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': cookie('XSRF-TOKEN') }
        })
          .then(function (r) { return r.json().catch(function () { return {}; }).then(function (d) { return { code: r.status, data: d }; }); })
          .then(function (res) {
            if (res.code === 200) {
              form.reset();
              count.textContent = '0';
              form.classList.add('sent');
              say(res.data.message || t('Thank you! Your review will show on the site after our team checks it.'), 'ok');
            } else if (res.code === 422 && res.data.errors) {
              showErrors(res.data.errors);
              say(res.data.message, 'err');
            } else if (res.code === 429) {
              say(t('Too many tries. Please wait a few minutes and try again.'), 'err');
            } else if (res.code === 419) {
              say(t('This page was open too long. Please reload the page and try again.'), 'err');
            } else {
              say(t('Could not send your review. Please try again.'), 'err');
            }
          })
          .catch(function () { say(t('Could not send your review. Please check your internet and try again.'), 'err'); })
          .then(function () { send.disabled = false; });
      });
    });
  }

  window.initReviewForms = initReviewForms;
  initReviewForms(document);
})();

/* Home "Our Services": while .svs-track scrolls past, .svs-pin stays on screen. The scroll position becomes
   --pp (0..1) on the section, which CSS uses to open the gaps between the layers; the active step is
   floor(pp * n): its layer slides out (.on), the layers before it are marked .past, and its panel, index
   button and caption are shown. Phones get every panel in a list (CSS), so nothing runs there. */
(function () {
  'use strict';
  var desktop = window.matchMedia('(min-width: 901px)');

  function initServiceScroll(scope) {
    scope.querySelectorAll('[data-svs]').forEach(function (sec) {
      if (sec.dataset.ready) return;
      sec.dataset.ready = '1';
      var track = sec.querySelector('.svs-track');
      var slabs = sec.querySelectorAll('[data-svs-slab]');
      var panels = sec.querySelectorAll('[data-svs-panel]');
      var steps = sec.querySelectorAll('[data-svs-step]');
      var cap = sec.querySelector('[data-svs-cap]');
      var n = slabs.length, active = -1, queued = false;
      if (!n) return;

      function progress() {
        var r = track.getBoundingClientRect();
        var span = r.height - window.innerHeight;
        return span > 0 ? Math.min(Math.max(-r.top / span, 0), 1) : 0;
      }

      function setActive(i) {
        if (i === active) return;
        active = i;
        slabs.forEach(function (s, k) { s.classList.toggle('on', k === i); s.classList.toggle('past', k < i); });
        panels.forEach(function (p, k) { p.classList.toggle('on', k === i); });
        steps.forEach(function (b, k) { b.classList.toggle('on', k === i); b.setAttribute('aria-selected', k === i ? 'true' : 'false'); });
        if (cap) {
          var title = slabs[i].querySelector('.svs-tt');
          cap.textContent = (i + 1 < 10 ? '0' : '') + (i + 1) + ' · ' + (title ? title.textContent : '');
        }
      }

      function render() {
        queued = false;
        if (!desktop.matches) return;
        var pp = progress();
        sec.style.setProperty('--pp', pp.toFixed(4));
        setActive(Math.min(n - 1, Math.floor(pp * n)));
      }
      function queue() { if (!queued) { queued = true; requestAnimationFrame(render); } }

      // An index button scrolls the page to the middle of that step.
      steps.forEach(function (b, i) {
        b.addEventListener('click', function () {
          var r = track.getBoundingClientRect();
          var span = r.height - window.innerHeight;
          window.scrollTo({ top: window.scrollY + r.top + span * (i + 0.5) / n, behavior: 'smooth' });
        });
      });

      window.addEventListener('scroll', queue, { passive: true });
      window.addEventListener('resize', queue);
      if (desktop.addEventListener) desktop.addEventListener('change', queue);
      render();
    });
  }

  window.initServiceScroll = initServiceScroll;
  initServiceScroll(document);
})();

/* Hero: lay the work images and the phone logo onto the white screens of the background photo.
   Each [data-quad] element keeps its own fixed size; a perspective transform (matrix3d) maps its
   four corners onto the four points in data-quad (fractions of the photo: TL TR BR BL). */
(function () {
  'use strict';
  var stage = document.querySelector('.hero-stage');
  if (!stage) return;
  var quads = stage.querySelectorAll('[data-quad]');

  function adj(m) {
    return [m[4] * m[8] - m[5] * m[7], m[2] * m[7] - m[1] * m[8], m[1] * m[5] - m[2] * m[4],
            m[5] * m[6] - m[3] * m[8], m[0] * m[8] - m[2] * m[6], m[2] * m[3] - m[0] * m[5],
            m[3] * m[7] - m[4] * m[6], m[1] * m[6] - m[0] * m[7], m[0] * m[4] - m[1] * m[3]];
  }
  function mul(a, b) {
    var c = [];
    for (var i = 0; i < 3; i++) for (var j = 0; j < 3; j++) {
      c[3 * i + j] = a[3 * i] * b[j] + a[3 * i + 1] * b[3 + j] + a[3 * i + 2] * b[6 + j];
    }
    return c;
  }
  function mulv(m, v) {
    return [m[0] * v[0] + m[1] * v[1] + m[2] * v[2], m[3] * v[0] + m[4] * v[1] + m[5] * v[2], m[6] * v[0] + m[7] * v[1] + m[8] * v[2]];
  }
  function basis(p) { // p: 4 points [x,y]
    var m = [p[0][0], p[1][0], p[2][0], p[0][1], p[1][1], p[2][1], 1, 1, 1];
    var v = mulv(adj(m), [p[3][0], p[3][1], 1]);
    return mul(m, [v[0], 0, 0, 0, v[1], 0, 0, 0, v[2]]);
  }

  function place() {
    var W = stage.clientWidth, H = stage.clientHeight;
    if (!W || !H) return;
    quads.forEach(function (el) {
      var w = el.offsetWidth, h = el.offsetHeight;
      var pts = el.getAttribute('data-quad').trim().split(/\s+/).map(function (s) {
        var xy = s.split(','); return [parseFloat(xy[0]) * W, parseFloat(xy[1]) * H];
      });
      // corner order for the maths: TL, TR, BL, BR
      var src = basis([[0, 0], [w, 0], [0, h], [w, h]]);
      var dst = basis([pts[0], pts[1], pts[3], pts[2]]);
      var t = mul(dst, adj(src));
      for (var i = 0; i < 9; i++) t[i] = t[i] / t[8];
      el.style.transform = 'matrix3d(' + [t[0], t[3], 0, t[6], t[1], t[4], 0, t[7], 0, 0, 1, 0, t[2], t[5], 0, t[8]].join(',') + ')';
      el.classList.add('is-placed');
    });
  }

  place();
  if (window.ResizeObserver) new ResizeObserver(place).observe(stage);
  else window.addEventListener('resize', place);
})();
/* Hero laptop: the Windows taskbar clock shows the visitor's real time and date. */
(function () {
  'use strict';
  var clock = document.querySelector('[data-win-clock]');
  if (!clock) return;
  var t = clock.querySelector('b'), d = clock.querySelector('small');
  function pad(n) { return (n < 10 ? '0' : '') + n; }
  function tick() {
    var now = new Date();
    t.textContent = now.getHours() % 12 === 0 ? '12:' + pad(now.getMinutes()) : (now.getHours() % 12) + ':' + pad(now.getMinutes());
    t.textContent += now.getHours() < 12 ? ' AM' : ' PM';
    d.textContent = pad(now.getDate()) + '/' + pad(now.getMonth() + 1) + '/' + now.getFullYear();
  }
  tick();
  setInterval(tick, 15000);
})();