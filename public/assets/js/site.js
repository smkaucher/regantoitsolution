/* Reganto IT Solution: vanilla JS replacing React/framer-motion behaviours. */
(function () {
  'use strict';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Navbar: services dropdown + mobile menu ---------- */
  document.querySelectorAll('[data-dd]').forEach(function (wrap) {
    var dd = wrap.querySelector('.dd');
    var link = wrap.querySelector(':scope > a');
    var show = function (v) { dd.hidden = !v; };
    wrap.addEventListener('mouseenter', function () { show(true); });
    wrap.addEventListener('mouseleave', function () { show(false); });
    link.addEventListener('click', function (e) {
      // On touch / mobile the first tap opens the menu instead of navigating.
      if (window.matchMedia('(max-width:900px)').matches && dd.hidden) { e.preventDefault(); show(true); }
    });
  });
  var menuBtn = document.getElementById('menuBtn');
  var pill = document.getElementById('navPill');
  if (menuBtn && pill) menuBtn.addEventListener('click', function () { pill.classList.toggle('open'); });

  /* ---------- Chat widget ---------- */
  var fab = document.getElementById('chatFab');
  var panel = document.getElementById('chatPanel');
  var ico = document.getElementById('chatIco');
  function setChat(open) {
    if (!fab || !panel) return;
    fab.classList.toggle('open', open);
    fab.setAttribute('aria-expanded', open ? 'true' : 'false');
    fab.setAttribute('aria-label', open ? 'Close live chat' : 'Open live chat');
    ico.textContent = open ? '✕' : '💬';
    if (open) { panel.hidden = false; requestAnimationFrame(function () { panel.classList.add('show'); }); }
    else { panel.classList.remove('show'); setTimeout(function () { if (!fab.classList.contains('open')) panel.hidden = true; }, 220); }
  }
  if (fab) {
    fab.addEventListener('click', function () { setChat(!fab.classList.contains('open')); });
    window.addEventListener('keydown', function (e) { if (e.key === 'Escape') setChat(false); });
  }

  /* ---------- Replay-in-view (same logic as useReplayInView) ----------
     Any [data-replay] element gets class "in" when >= threshold visible and
     loses it once fully out of view, so animations replay on every scroll in.
     data-threshold="0.3" overrides the default. */
  var replayEls = document.querySelectorAll('[data-replay]');
  function setIn(el, v) {
    if (el.classList.contains('in') === v) return;
    el.classList.toggle('in', v);
    el.dispatchEvent(new CustomEvent(v ? 'replay:in' : 'replay:out'));
  }
  if (!('IntersectionObserver' in window) || reduce) {
    replayEls.forEach(function (el) { setIn(el, true); });
  } else {
    replayEls.forEach(function (el) {
      var th = parseFloat(el.dataset.threshold || '0.3');
      new IntersectionObserver(function (entries) {
        var en = entries[0];
        if (en.intersectionRatio >= th) setIn(el, true);
        else if (!en.isIntersecting) setIn(el, false);
      }, { threshold: [0, th] }).observe(el);
    });
  }

  /* ---------- Count-up: <span data-count="80" data-suffix="+" data-decimals="0"> ----------
     Counts 0 -> value when its nearest [data-replay] ancestor (or itself) goes "in"; resets when out. */
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var value = parseFloat(el.dataset.count), dec = parseInt(el.dataset.decimals || '0', 10);
    var suffix = el.dataset.suffix || '', dur = parseFloat(el.dataset.duration || '2') * 1000;
    var host = el.closest('[data-replay]') || el, raf;
    var paint = function (v) { el.textContent = v.toFixed(dec) + suffix; };
    var run = function () {
      cancelAnimationFrame(raf);
      if (reduce) return paint(value);
      var t0 = performance.now();
      (function step(t) {
        var p = Math.min((t - t0) / dur, 1), e = 1 - Math.pow(1 - p, 3); // easeOut
        paint(value * e);
        if (p < 1) raf = requestAnimationFrame(step);
      })(t0);
    };
    paint(reduce ? value : 0);
    host.addEventListener('replay:in', run);
    host.addEventListener('replay:out', function () { cancelAnimationFrame(raf); paint(0); });
    if (host.classList.contains('in')) run();
  });

  /* ---------- Typing text: <span data-typing='["Prompt 1","Prompt 2"]'></span> ---------- */
  document.querySelectorAll('[data-typing]').forEach(function (el) {
    var prompts; try { prompts = JSON.parse(el.dataset.typing); } catch (e) { return; }
    if (!prompts.length) return;
    var caret = document.createElement('i'); if (el.dataset.caret) caret.className = el.dataset.caret;
    var txt = document.createTextNode('');
    el.textContent = ''; el.appendChild(txt); el.appendChild(caret);
    if (reduce) { txt.nodeValue = prompts[0]; return; }
    var i = 0, pos = 0, del = false;
    (function tick() {
      var full = prompts[i], d;
      if (!del) {
        pos++; txt.nodeValue = full.slice(0, pos);
        if (pos === full.length) { del = true; d = 1700; } else d = 65;
      } else {
        pos--; txt.nodeValue = full.slice(0, pos);
        if (pos === 0) { del = false; i = (i + 1) % prompts.length; d = 450; } else d = 28;
      }
      setTimeout(tick, d);
    })();
  });

  /* ---------- Words reveal: <h2 data-words>text</h2> (word-by-word, staggered) ---------- */
  document.querySelectorAll('[data-words]').forEach(function (el) {
    var words = el.textContent.trim().split(/\s+/), step = parseFloat(el.dataset.stagger || '0.06');
    el.textContent = '';
    el.classList.add('rw');
    words.forEach(function (w, i) {
      var s = document.createElement('span'); s.className = 'rw-word'; s.textContent = w;
      s.style.transitionDelay = (i * step) + 's';
      el.appendChild(s); if (i < words.length - 1) el.appendChild(document.createTextNode(' '));
    });
    if (!el.closest('[data-replay]') && !el.hasAttribute('data-replay')) el.setAttribute('data-replay', '');
  });
  // Re-query replay elements added by the words step.
  document.querySelectorAll('[data-words][data-replay]:not(.in)').forEach(function (el) {
    if (!('IntersectionObserver' in window) || reduce) { el.classList.add('in'); return; }
    new IntersectionObserver(function (entries) {
      var en = entries[0];
      if (en.intersectionRatio >= 0.3) setIn(el, true); else if (!en.isIntersecting) setIn(el, false);
    }, { threshold: [0, 0.3] }).observe(el);
  });

  /* ---------- Fade-up blocks: <div data-fade> (stagger children with data-fade-stagger) ---------- */
  document.querySelectorAll('[data-fade]').forEach(function (el) {
    if (el.hasAttribute('data-replay')) return;
    el.setAttribute('data-replay', '');
    if (!('IntersectionObserver' in window) || reduce) { el.classList.add('in'); return; }
    new IntersectionObserver(function (entries) {
      var en = entries[0];
      if (en.intersectionRatio >= 0.15) setIn(el, true); else if (!en.isIntersecting) setIn(el, false);
    }, { threshold: [0, 0.15] }).observe(el);
  });
  document.querySelectorAll('[data-fade-stagger] > *').forEach(function (c, i) { c.style.transitionDelay = (i * 0.08) + 's'; });
})();
