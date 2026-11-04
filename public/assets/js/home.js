/* Reganto IT Solution: home page interactions (services tabs, work filter + previews, reviews carousel, scroll button). */
(function () {
  'use strict';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- "Scroll to discover": smooth scroll to the next section ---------- */
  document.querySelectorAll('[data-scroll-next]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var sec = btn.closest('section'), next = sec && sec.nextElementSibling;
      if (next) next.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  /* ---------- Services tabs ---------- */
  document.querySelectorAll('[data-tabs]').forEach(function (root) {
    var tabs = root.querySelectorAll('[data-tab]'), panels = root.querySelectorAll('[data-panel]');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        var id = tab.getAttribute('data-tab');
        tabs.forEach(function (t) {
          var on = t === tab;
          t.classList.toggle('on', on);
          t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        panels.forEach(function (p) { p.hidden = p.getAttribute('data-panel') !== id; });
      });
    });
  });

  /* ---------- Work: category filter + live site previews ---------- */
  document.querySelectorAll('[data-work]').forEach(function (sec) {
    var btns = sec.querySelectorAll('.tabs [data-cat]'), cards = sec.querySelectorAll('.wk-card');
    var empty = sec.querySelector('[data-empty]');
    btns.forEach(function (b) {
      b.addEventListener('click', function () {
        var cat = b.getAttribute('data-cat'), shown = 0;
        btns.forEach(function (x) { x.classList.toggle('on', x === b); });
        cards.forEach(function (c) {
          var ok = cat === 'All' || c.getAttribute('data-cat') === cat;
          c.hidden = !ok;
          if (ok) shown++;
        });
        if (empty) { empty.hidden = shown > 0; empty.textContent = 'No ' + cat + ' projects found.'; }
      });
    });

    // Screenshot service answers with a small "generating" placeholder first; retry a few times.
    sec.querySelectorAll('img[data-shot]').forEach(function (img) {
      var url = img.getAttribute('data-shot'), tries = 0, MAX = 6;
      var fallback = img.parentNode.querySelector('.wk-fallback');
      var src = function () {
        return 'https://s.wordpress.com/mshots/v1/' + encodeURIComponent(url) + '?w=1280&h=800' + (tries ? '&r=' + tries : '');
      };
      img.addEventListener('load', function () {
        if (img.naturalWidth < 600) {
          if (tries < MAX) { tries++; setTimeout(function () { img.src = src(); }, 3500); }
          else { img.remove(); if (fallback) fallback.hidden = false; }
        } else if (fallback) { fallback.hidden = true; }
      });
      img.addEventListener('error', function () { img.remove(); if (fallback) fallback.hidden = false; });
      img.src = src();
    });
  });

  /* ---------- Reviews 3D carousel ---------- */
  document.querySelectorAll('[data-carousel]').forEach(function (root) {
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
    if (!reduce) setInterval(function () { if (!paused) go(active + 1); }, 4500);
  });
})();
