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
