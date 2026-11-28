/* /home2: the travelling laptop (#dv).
   Desktop: it is fixed to the viewport and placed by blending the rectangles of "slots" on the page:
     hero slot  --(scroll to Services: the keyboard folds away = a monitor)-->  Services slot (while pinned)
     --(leaving Services: back to a small laptop)-->  bottom-left corner  --(near the end)-->  dock slot above the footer.
   Its screen follows: the mini website, the active service (in step with home.js's pinned section), the section being
   read, then "Let's build yours". Phones/tablets and reduced motion: it simply sits in the hero slot.

   Smooth on slow devices: the page is measured only when its size changes (not every frame); the laptop glides
   towards where the scroll says it should be (eased per frame, by elapsed time), and the loop stops once it
   has arrived. The 3D model (home2-3d.js) loads only on capable desktops, after the page has finished loading. */
(function () {
  'use strict';
  var dv = document.getElementById('dv');
  var hero = document.querySelector('[data-h2-hero]');
  if (!dv || !hero) return;

  var W = 600;                                       // the CSS laptop's own width in px (see home2.css)
  var HERO_TURN = 0;                                 // degrees of turn in the hero (0 = facing the viewer straight on)
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var desktop = window.matchMedia('(min-width:1001px)');
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var slots = { hero: $('[data-dv-slot=hero]'), svc: $('[data-dv-slot=services]'), dock: $('[data-dv-slot=dock]') };
  var svs = $('[data-svs]'), track = svs && $('.svs-track', svs);
  var sitePanels = svs ? svs.querySelectorAll('[data-svs-panel]') : [];
  var screens = {};
  Array.prototype.forEach.call(dv.querySelectorAll('[data-scr]'), function (s) { screens[s.getAttribute('data-scr')] = s; });
  var panels = dv.querySelectorAll('[data-dv-panel]'), svCount = $('[data-sv-count]', dv), svProg = $('[data-sv-progress]', dv);
  var miniLabel = $('[data-mini-label]', dv), miniProg = $('[data-mini-progress]', dv);
  var parts = document.querySelectorAll('[data-h2-part]');
  var LABELS = { work: 'Our work', features: 'Why us', reviews: 'Reviews', faq: 'FAQ' };
  var current = '', active = -1, intro = 0, moving = false, home = dv.parentNode;
  var three = null;                                  // the 3D laptop once loaded; until then (or never) the CSS laptop
  var M = {};                                        // page measurements, refreshed only when the page changes size
  var cur = null, raf = 0, last = 0;                 // the drawn pose and the animation loop

  function clamp(v) { return v < 0 ? 0 : v > 1 ? 1 : v; }
  function ease(t) { return t < .5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2; }
  function lerp(a, b, t) { return a + (b - a) * t; }
  function box(el) { var r = el.getBoundingClientRect(); return { x: r.left, y: r.top, w: r.width }; }
  function mix(a, b, t) { return { x: lerp(a.x, b.x, t), y: lerp(a.y, b.y, t), w: lerp(a.w, b.w, t) }; }
  function show(name) {
    if (current === name) return;
    current = name;
    for (var k in screens) screens[k].classList.toggle('on', k === name);
  }

  /* ---------- Measuring (only on load, resize and when lazy sections change the page height) ---------- */
  function measure() {
    var y = window.scrollY, top = function (el) { return el ? el.getBoundingClientRect().top + y : 0; };
    M.vh = window.innerHeight;
    M.heroH = hero.offsetHeight;
    M.svsTop = svs ? top(svs) : M.heroH + M.vh;
    M.trackH = track ? track.offsetHeight : 0;
    M.dockTop = slots.dock ? top(slots.dock) : M.svsTop + M.trackH + 2 * M.vh;
    M.dockH = slots.dock ? slots.dock.offsetHeight : 0;
  }

  /* Services screen: the same panel as the pinned section's active one */
  function syncServices() {
    var on = 0;
    for (var i = 0; i < sitePanels.length; i++) if (sitePanels[i].classList.contains('on')) { on = i; break; }
    if (on !== active) {
      active = on;
      Array.prototype.forEach.call(panels, function (p, i) { p.classList.toggle('on', i === on); });
      if (svCount) svCount.textContent = (on + 1 < 10 ? '0' : '') + (on + 1) + ' / ' + (panels.length < 10 ? '0' : '') + panels.length;
    }
    if (svProg && svs) svProg.style.setProperty('--p', parseFloat(svs.style.getPropertyValue('--pp')) || 0);
  }

  /* ---------- Where the laptop should be now (the target), from the scroll position ---------- */
  function target() {
    var vh = M.vh, y = window.scrollY;
    var A = box(slots.hero);
    if (!moving) return { r: A, lid: lerp(92, 0, intro), base: 0, tilt: 0, scr: 'site', anchor: slots.hero };

    var B = slots.svc ? box(slots.svc) : A, D = slots.dock ? box(slots.dock) : A;
    var mw = Math.min(200, window.innerWidth * .14);
    var C = { x: 22, y: vh - 26 - mw * .82, w: mw };            // room for the screen and the keyboard below it
    var y1 = M.svsTop, y2 = y1 + Math.max(0, M.trackH - vh), y3 = y2 + vh * .55;
    // It stays in the hero until the Services section starts to come into view, then makes the trip
    var y0 = Math.min(y1 - 80, Math.max(M.heroH * .3, y1 - vh * .55));
    var y4 = Math.max(y3 + 1, M.dockTop - vh);
    var y5 = Math.max(y4 + 60, M.dockTop - (vh - M.dockH) / 2 - 30);
    // anchor: resting in a section, the laptop is drawn inside that section's slot, so it scrolls with the page
    // exactly (no lag, no shaking); null = on the way between sections, drawn over the window
    var t = { lid: lerp(92, 0, intro), base: 0, tilt: 0, anchor: null }, e;

    if (y <= y0) {                                              // resting in the hero
      t.r = A; t.scr = 'site'; t.anchor = slots.hero; t.tilt = HERO_TURN;
      hero.classList.remove('away');
    } else if (y < y1) {                                        // hero -> Services: fold into a monitor
      e = ease(clamp((y - y0) / (y1 - y0)));
      // in the hero the laptop is turned a little to the left; it straightens as it folds into the monitor
      t.r = mix(A, B, e); t.base = clamp(e * 1.8); t.tilt = Math.sin(e * Math.PI) * -16 + (1 - e) * HERO_TURN;
      t.scr = e > .5 ? 'svc' : 'site';
      hero.classList.toggle('away', e > .1);
    } else if (y <= y2) {                                       // pinned: the monitor shows the services
      t.r = B; t.base = 1; t.scr = 'svc'; t.anchor = slots.svc;
    } else if (y < y3) {                                        // leaving: back to a small laptop
      e = ease(clamp((y - y2) / (y3 - y2)));
      t.r = mix(B, C, e); t.base = 1 - clamp(e * 1.5 - .3); t.tilt = Math.sin(e * Math.PI) * 14;
      t.scr = e > .45 ? 'mini' : 'svc';
    } else if (y < y4) {                                        // riding along in the corner
      t.r = C; t.scr = 'mini';
    } else if (y < y5) {                                        // landing above the footer
      e = ease(clamp((y - y4) / (y5 - y4)));
      t.r = mix(C, D, e); t.tilt = Math.sin(e * Math.PI) * -12;
      t.lid = Math.max(t.lid, Math.sin(e * Math.PI) * 28);      // a small close-and-open as it lands
      t.scr = e > .55 ? 'cta' : 'mini';
    } else {                                                    // landed: resting above the footer
      t.r = D; t.scr = 'cta'; t.anchor = slots.dock;
    }
    if (y >= y1) hero.classList.add('away');
    if (t.scr === 'mini') {
      var mid = vh / 2, label = null;
      // the last section whose top has passed the middle of the screen
      Array.prototype.forEach.call(parts, function (p) { if (p.getBoundingClientRect().top <= mid) label = LABELS[p.getAttribute('data-h2-part')]; });
      if (label && window.t) label = window.t(label);
      if (miniLabel && label && miniLabel.textContent !== label) miniLabel.textContent = label;
      if (miniProg) miniProg.style.setProperty('--p', clamp((y - y3) / (y4 - y3)).toFixed(3));
    }
    return t;
  }

  /* Draw the pose: the 3D model if loaded, else the CSS laptop. With an anchor (a slot) the laptop is placed
     inside that slot and the browser scrolls it with the page; without one it is fixed over the window. */
  function draw(p, anchor) {
    if (three) { three.apply({ x: p.x, y: p.y, w: p.w, lid: p.lid, base: p.base, tilt: p.tilt, anchor: anchor || null }); return; }
    if (anchor) {
      if (dv.parentNode !== anchor) anchor.appendChild(dv);
      dv.classList.add('is-static');
      dv.style.transform = 'scale(' + (anchor.clientWidth / W).toFixed(4) + ')';
    } else {
      if (dv.parentNode !== home) home.appendChild(dv);
      dv.classList.remove('is-static');
      dv.style.transform = 'translate3d(' + p.x.toFixed(1) + 'px,' + p.y.toFixed(1) + 'px,0) scale(' + (p.w / W).toFixed(4) + ')';
    }
    dv.style.setProperty('--lid', p.lid.toFixed(2) + 'deg');
    dv.style.setProperty('--base', p.base.toFixed(3));
    dv.style.setProperty('--tilt', p.tilt.toFixed(2) + 'deg');
  }

  /* ---------- One frame: the laptop is exactly where the scroll says (no easing, so it never lags the page) ---------- */
  function frame() {
    raf = 0;
    var t = target();
    if (moving || three) draw({ x: t.r.x, y: t.r.y, w: t.r.w, lid: t.lid, base: t.base, tilt: t.tilt }, t.anchor);
    if (intro > .75) show(t.scr);
    if (t.scr === 'svc') syncServices();
  }
  function schedule() { if (!raf) raf = window.requestAnimationFrame(frame); }
  function kick() { last = 0; schedule(); }

  /* ---------- Phones / reduced motion without 3D: the CSS laptop sits in the hero slot ---------- */
  function placeStatic() {
    if (dv.parentNode !== slots.hero) slots.hero.appendChild(dv);
    dv.classList.add('is-static');
    dv.style.transform = 'scale(' + (slots.hero.clientWidth / W).toFixed(4) + ')';
    dv.style.setProperty('--lid', lerp(92, 0, intro).toFixed(2) + 'deg');
    dv.style.setProperty('--base', '0');
    dv.style.setProperty('--tilt', '0deg');
  }

  function mode() {
    moving = desktop.matches && !reduce && !!slots.svc;
    measure();
    if (moving || three) {
      kick(); // draw() puts the laptop where it belongs (the 3D module manages its own layer)
    } else {
      placeStatic();
      hero.classList.remove('away');
      if (intro > .75) show('site');
    }
  }

  /* ---------- Opening: the laptop appears shut, the lid swings up, the screen lights, the screens fan out ----------
     Runs once, with whichever laptop is shown (the 3D model when it is coming, else the CSS one), so the visitor
     never sees one laptop and then another: it always starts folded. */
  var opened = false;
  function openLid() {
    if (opened) return;
    opened = true;
    intro = 0;
    dv.classList.add('ready');
    if (reduce) { intro = 1; dv.classList.add('lit'); hero.classList.add('mons'); show('site'); (moving || three) ? kick() : placeStatic(); return; }
    var start = null;
    function step(t) {
      if (start === null) start = t;
      intro = ease(clamp((t - start - 350) / 1500));
      if (intro > .55) hero.classList.add('mons');
      if (intro > .75 && !dv.classList.contains('lit')) { dv.classList.add('lit'); show('site'); }
      (moving || three) ? kick() : placeStatic();
      if (intro < 1) window.requestAnimationFrame(step);
    }
    window.requestAnimationFrame(step);
  }

  /* ---------- The 3D model: draws the laptop from now on; the HTML screen is mapped onto its screen ---------- */
  var screenEl = $('.dv-screen', dv), screenHome = screenEl.parentNode;
  function use3d() {
    if (three || !window.H2_3D) return;
    if (opened) return; // too late: the CSS laptop has already opened, keep it (never show a second laptop)
    three = window.H2_3D;
    dv.appendChild(screenEl);
    dv.classList.add('dv--3d');
    mode();
    openLid(); // the 3D laptop has arrived: it starts shut and opens
  }
  // The GPU gave up (old or busy device): back to the CSS laptop, nothing breaks.
  function drop3d() {
    if (!three) return;
    three = null;
    screenEl.style.transform = '';
    screenEl.style.visibility = '';
    screenHome.appendChild(screenEl);
    dv.classList.remove('dv--3d');
    home.appendChild(dv); // out of the 3D layer, back to the page
    mode();
  }
  window.addEventListener('h2-3d-ready', use3d);
  window.addEventListener('h2-3d-lost', drop3d);

  // Only on capable desktops, and only once the page itself has loaded: a slow network or a weak device keeps
  // the light CSS laptop and never downloads Three.js.
  function can3d() {
    if (!moving || !window.H2_3D_SRC) return false;
    var c = navigator.connection || {};
    if (c.saveData || /(^|-)2g$/.test(c.effectiveType || '')) return false;
    if ((navigator.deviceMemory && navigator.deviceMemory < 4) || (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4)) return false;
    return true;
  }
  function load3d() {
    var go = function () {
      try { (new Function('u', 'return import(u)'))(window.H2_3D_SRC).catch(function () { openLid(); }); } catch (e) { openLid(); }
    };
    var idle = function () {
      (window.requestIdleCallback || function (f) { setTimeout(f, 200); })(go, { timeout: 700 });
      // if the 3D laptop has not arrived a few seconds after the page loaded, open the CSS one instead
      setTimeout(openLid, 6000);
    };
    if (document.readyState === 'complete') idle(); else window.addEventListener('load', idle, { once: true });
  }

  /* ---------- Hero carousel: a coverflow of glass screens around the laptop (like the reviews deck) ----------
     Glass screens [data-cv]: place d = -2..2 around the laptop (0 = inside the laptop: it slides in behind the glass
     and fades, while the laptop screen shows that service). A step moves every screen one place to the left.
     The laptop screen's own cards ([data-sl-track="0"]): .on slides in from the right, .out slides away left. */
  (function carousel() {
    var tracks = Array.prototype.map.call(document.querySelectorAll('[data-sl-track]'), function (t) {
      return { off: parseInt(t.getAttribute('data-sl-track'), 10) || 0, cards: t.querySelectorAll('[data-sl-card]') };
    });
    var ring = document.querySelectorAll('[data-cv]');
    var dots = dv.querySelectorAll('[data-sl-dot]'), stage = $('[data-h2-stage]');
    var n = tracks.length ? tracks[0].cards.length : ring.length, idx = 0, paused = false, timers = [];
    if (n < 2) return;
    function place(first) {
      Array.prototype.forEach.call(ring, function (c, k) {
        var d = ((k - idx) % n + n) % n;
        if (d > n / 2) d -= n;
        var a = Math.abs(d), prev = c._d;
        // a screen going round from one end to the other jumps there hidden instead of flying across
        var wrap = !first && prev !== undefined && Math.abs(d - prev) > 1;
        if (wrap) { c.classList.add('jump'); c.style.opacity = '0'; }
        c.style.setProperty('--d', d);
        c.style.setProperty('--a', a);
        c.style.setProperty('--s', d === 0 ? 0 : d > 0 ? 1 : -1);
        c.style.zIndex = 10 - a;
        c.setAttribute('aria-hidden', a === 0 || a > 2 ? 'true' : 'false');
        c.tabIndex = a === 1 ? 0 : -1;
        var op = a === 0 ? 0 : a === 1 ? 1 : a === 2 ? .5 : 0;
        if (wrap) { void c.offsetWidth; c.classList.remove('jump'); requestAnimationFrame(function () { c.style.opacity = op; }); }
        else c.style.opacity = op;
        c._d = d;
      });
    }
    function go(i, first) {
      idx = ((i % n) + n) % n;
      place(first);
      tracks.forEach(function (tr) {
        var want = ((idx + tr.off) % n + n) % n;
        Array.prototype.forEach.call(tr.cards, function (c, k) {
          var was = c.classList.contains('on');
          c.classList.toggle('on', k === want);
          c.classList.toggle('out', !first && was && k !== want);
        });
      });
      Array.prototype.forEach.call(dots, function (d, k) { d.classList.toggle('on', k === idx); });
      // once a card has slid away, park it back on the right (hidden) so it can come in from the right again
      timers.forEach(clearTimeout);
      timers = [setTimeout(function () {
        tracks.forEach(function (tr) { Array.prototype.forEach.call(tr.cards, function (c) { if (!c.classList.contains('on')) c.classList.remove('out'); }); });
      }, 900)];
    }
    go(0, true);
    // waits while the mouse is on it, the hero is off screen or scrolled away, or the tab is hidden
    if (stage) {
      stage.addEventListener('mouseenter', function () { paused = true; });
      stage.addEventListener('mouseleave', function () { paused = false; });
    }
    window.setInterval(function () {
      if (paused || document.hidden || hero.classList.contains('off') || hero.classList.contains('away')) return;
      go(idx + 1);
    }, reduce ? 6000 : 3800);
  })();

  // Pause the hero's looping animations while it is off screen
  if (window.IntersectionObserver) {
    new IntersectionObserver(function (es) { hero.classList.toggle('off', !es[0].isIntersecting); }).observe(hero);
  }

  hero.classList.add('go');           // the headline and text come in at once
  mode();
  use3d();                            // (if the 3D module somehow loaded already)
  // The laptop: wait for the 3D model where it will come, so only one laptop ever appears and it opens from shut
  if (!opened) { if (can3d()) load3d(); else openLid(); }
  window.addEventListener('scroll', function () { if (moving || three) schedule(); }, { passive: true });
  window.addEventListener('resize', function () { measure(); (moving || three) ? kick() : placeStatic(); });
  if (desktop.addEventListener) desktop.addEventListener('change', mode);
  // Lazy sections below change the page height: measure again (once per frame at most)
  if (window.ResizeObserver) {
    var mq = 0;
    new ResizeObserver(function () { if (!mq) mq = window.requestAnimationFrame(function () { mq = 0; measure(); if (moving || three) schedule(); }); }).observe(document.body);
  }
})();
