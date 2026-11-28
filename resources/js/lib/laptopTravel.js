/* Home page: the travelling laptop (#dv). The logic of public/assets/js/home2.js, unchanged in what it does,
   rewritten so it starts on the React page and stops cleanly when the page is left (no listener, timer, frame,
   observer or WebGL context is left behind).

   Desktop: it is fixed to the viewport and placed by blending the rectangles of "slots" on the page:
     hero slot  --(scroll to Services: the keyboard folds away = a monitor)-->  Services slot (while pinned)
     --(leaving Services: back to a small laptop)-->  bottom-left corner  --(near the end)-->  dock slot above the footer.
   Its screen follows: the active service, the Services panels (in step with the pinned section), the section being
   read, then "Let's build yours". Phones/tablets and reduced motion: it simply sits in the hero slot.
   The 3D model (laptop3d.js, with Three.js) is downloaded only on capable desktops, after the page has loaded.

   start({ dv, home, page }) -> cleanup.  dv: the #dv element; home: where #dv lives when it is drawn over the
   window; page: the page root (the hero, slots and sections are looked up inside it). */
export function startLaptop({ dv, home, page }) {
    const hero = page.querySelector('[data-h2-hero]');
    if (!dv || !hero) return () => {};

    const W = 600;                                       // the CSS laptop's own width in px (see home2.css)
    const HERO_TURN = 0;                                 // degrees of turn in the hero
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const desktop = window.matchMedia('(min-width:1001px)');
    const $ = (s, r) => (r || page).querySelector(s);
    const slots = { hero: $('[data-dv-slot=hero]'), svc: $('[data-dv-slot=services]'), dock: $('[data-dv-slot=dock]') };
    const svs = $('[data-svs]'), track = svs && $('.svs-track', svs);
    const sitePanels = svs ? svs.querySelectorAll('[data-svs-panel]') : [];
    const screens = {};
    dv.querySelectorAll('[data-scr]').forEach((s) => { screens[s.getAttribute('data-scr')] = s; });
    const panels = dv.querySelectorAll('[data-dv-panel]'), svCount = $('[data-sv-count]', dv), svProg = $('[data-sv-progress]', dv);
    const miniLabel = $('[data-mini-label]', dv), miniProg = $('[data-mini-progress]', dv);
    const LABELS = { work: 'Our work', features: 'Why us', reviews: 'Reviews', faq: 'FAQ' };
    let current = '', active = -1, intro = 0, moving = false;
    let three = null;                                    // the 3D laptop once loaded; until then (or never) the CSS laptop
    const M = {};                                        // page measurements, refreshed only when the page changes size
    let raf = 0, stopped = false;
    const timers = [], intervals = [], offs = [];
    const on = (t, type, fn, opts) => { t.addEventListener(type, fn, opts); offs.push(() => t.removeEventListener(type, fn, opts)); };

    const clamp = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
    const ease = (t) => (t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2);
    const lerp = (a, b, t) => a + (b - a) * t;
    const box = (el) => { const r = el.getBoundingClientRect(); return { x: r.left, y: r.top, w: r.width }; };
    const mix = (a, b, t) => ({ x: lerp(a.x, b.x, t), y: lerp(a.y, b.y, t), w: lerp(a.w, b.w, t) });
    const parts = () => page.querySelectorAll('[data-h2-part]'); // sections can arrive later (deferred data)
    function show(name) {
        if (current === name) return;
        current = name;
        for (const k in screens) screens[k].classList.toggle('on', k === name);
    }

    /* ---------- Measuring (only on load, resize and when the page height changes) ---------- */
    function measure() {
        const y = window.scrollY, top = (el) => (el ? el.getBoundingClientRect().top + y : 0);
        M.vh = window.innerHeight;
        M.heroH = hero.offsetHeight;
        M.svsTop = svs ? top(svs) : M.heroH + M.vh;
        M.trackH = track ? track.offsetHeight : 0;
        M.dockTop = slots.dock ? top(slots.dock) : M.svsTop + M.trackH + 2 * M.vh;
        M.dockH = slots.dock ? slots.dock.offsetHeight : 0;
    }

    /* Services screen: the same panel as the pinned section's active one */
    function syncServices() {
        let idx = 0;
        for (let i = 0; i < sitePanels.length; i++) if (sitePanels[i].classList.contains('on')) { idx = i; break; }
        if (idx !== active) {
            active = idx;
            panels.forEach((p, i) => p.classList.toggle('on', i === idx));
            if (svCount) svCount.textContent = (idx + 1 < 10 ? '0' : '') + (idx + 1) + ' / ' + (panels.length < 10 ? '0' : '') + panels.length;
        }
        if (svProg && svs) svProg.style.setProperty('--p', parseFloat(svs.style.getPropertyValue('--pp')) || 0);
    }

    /* ---------- Where the laptop should be now, from the scroll position ---------- */
    function target() {
        const vh = M.vh, y = window.scrollY;
        const A = box(slots.hero);
        if (!moving) return { r: A, lid: lerp(92, 0, intro), base: 0, tilt: 0, scr: 'site', anchor: slots.hero };

        const B = slots.svc ? box(slots.svc) : A, D = slots.dock ? box(slots.dock) : A;
        const mw = Math.min(200, window.innerWidth * 0.14);
        const C = { x: 22, y: vh - 26 - mw * 0.82, w: mw };
        const y1 = M.svsTop, y2 = y1 + Math.max(0, M.trackH - vh), y3 = y2 + vh * 0.55;
        const y0 = Math.min(y1 - 80, Math.max(M.heroH * 0.3, y1 - vh * 0.55));
        const y4 = Math.max(y3 + 1, M.dockTop - vh);
        const y5 = Math.max(y4 + 60, M.dockTop - (vh - M.dockH) / 2 - 30);
        const t = { lid: lerp(92, 0, intro), base: 0, tilt: 0, anchor: null };
        let e;

        if (y <= y0) {                                              // resting in the hero
            t.r = A; t.scr = 'site'; t.anchor = slots.hero; t.tilt = HERO_TURN;
            hero.classList.remove('away');
        } else if (y < y1) {                                        // hero -> Services: fold into a monitor
            e = ease(clamp((y - y0) / (y1 - y0)));
            t.r = mix(A, B, e); t.base = clamp(e * 1.8); t.tilt = Math.sin(e * Math.PI) * -16 + (1 - e) * HERO_TURN;
            t.scr = e > 0.5 ? 'svc' : 'site';
            hero.classList.toggle('away', e > 0.1);
        } else if (y <= y2) {                                       // pinned: the monitor shows the services
            t.r = B; t.base = 1; t.scr = 'svc'; t.anchor = slots.svc;
        } else if (y < y3) {                                        // leaving: back to a small laptop
            e = ease(clamp((y - y2) / (y3 - y2)));
            t.r = mix(B, C, e); t.base = 1 - clamp(e * 1.5 - 0.3); t.tilt = Math.sin(e * Math.PI) * 14;
            t.scr = e > 0.45 ? 'mini' : 'svc';
        } else if (y < y4) {                                        // riding along in the corner
            t.r = C; t.scr = 'mini';
        } else if (y < y5) {                                        // landing above the footer
            e = ease(clamp((y - y4) / (y5 - y4)));
            t.r = mix(C, D, e); t.tilt = Math.sin(e * Math.PI) * -12;
            t.lid = Math.max(t.lid, Math.sin(e * Math.PI) * 28);
            t.scr = e > 0.55 ? 'cta' : 'mini';
        } else {                                                    // landed: resting above the footer
            t.r = D; t.scr = 'cta'; t.anchor = slots.dock;
        }
        if (y >= y1) hero.classList.add('away');
        if (t.scr === 'mini') {
            const mid = vh / 2;
            let label = null;
            parts().forEach((p) => { if (p.getBoundingClientRect().top <= mid) label = LABELS[p.getAttribute('data-h2-part')]; });
            if (label && window.t) label = window.t(label);
            if (miniLabel && label && miniLabel.textContent !== label) miniLabel.textContent = label;
            if (miniProg) miniProg.style.setProperty('--p', clamp((y - y3) / (y4 - y3)).toFixed(3));
        }
        return t;
    }

    /* Draw the pose: the 3D model if loaded, else the CSS laptop. With an anchor (a slot) the laptop sits inside
       that slot and scrolls with the page; without one it is fixed over the window. */
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
        if (stopped) return;
        const t = target();
        if (moving || three) draw({ x: t.r.x, y: t.r.y, w: t.r.w, lid: t.lid, base: t.base, tilt: t.tilt }, t.anchor);
        if (intro > 0.75) show(t.scr);
        if (t.scr === 'svc') syncServices();
    }
    const schedule = () => { if (!raf && !stopped) raf = window.requestAnimationFrame(frame); };
    const kick = schedule;

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
        if (moving || three) kick();
        else {
            placeStatic();
            hero.classList.remove('away');
            if (intro > 0.75) show('site');
        }
    }

    /* ---------- Opening: shut, the lid swings up, the screen lights, the screens fan out. Runs once, with whichever
       laptop is shown, so the visitor never sees one laptop and then another. ---------- */
    let opened = false, introRaf = 0;
    function openLid() {
        if (opened || stopped) return;
        opened = true;
        intro = 0;
        dv.classList.add('ready');
        if (reduce) { intro = 1; dv.classList.add('lit'); hero.classList.add('mons'); show('site'); if (moving || three) kick(); else placeStatic(); return; }
        let start = null;
        const step = (now) => {
            if (stopped) return;
            if (start === null) start = now;
            intro = ease(clamp((now - start - 350) / 1500));
            if (intro > 0.55) hero.classList.add('mons');
            if (intro > 0.75 && !dv.classList.contains('lit')) { dv.classList.add('lit'); show('site'); }
            if (moving || three) kick(); else placeStatic();
            if (intro < 1) introRaf = window.requestAnimationFrame(step);
        };
        introRaf = window.requestAnimationFrame(step);
    }

    /* ---------- The 3D model: draws the laptop from now on; the HTML screen is mapped onto its screen ---------- */
    const screenEl = $('.dv-screen', dv), screenHome = screenEl.parentNode;
    function use3d(api) {
        if (three || stopped) { if (stopped && api) api.dispose(); return; }
        if (opened) { api.dispose(); return; } // too late: the CSS laptop has already opened, keep it
        three = api;
        dv.appendChild(screenEl);
        dv.classList.add('dv--3d');
        mode();
        openLid();
    }
    // The GPU gave up (old or busy device): back to the CSS laptop, nothing breaks.
    function drop3d() {
        if (!three) return;
        three = null;
        screenEl.style.transform = '';
        screenEl.style.visibility = '';
        screenHome.appendChild(screenEl);
        dv.classList.remove('dv--3d');
        home.appendChild(dv);
        mode();
    }

    function can3d() {
        if (!moving) return false;
        const c = navigator.connection || {};
        if (c.saveData || /(^|-)2g$/.test(c.effectiveType || '')) return false;
        if ((navigator.deviceMemory && navigator.deviceMemory < 4) || (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4)) return false;
        return true;
    }
    let idleId = 0;
    function load3d() {
        const go = () => {
            import('./laptop3d.js')
                .then((m) => { if (stopped) return; const api = m.bootLaptop3D({ dv, screenEl, onLost: drop3d }); if (api) use3d(api); else openLid(); })
                .catch(() => openLid());
        };
        const idle = () => {
            if (stopped) return;
            idleId = (window.requestIdleCallback || ((f) => setTimeout(f, 200)))(go, { timeout: 700 });
            // if the 3D laptop has not arrived a few seconds after the page loaded, open the CSS one instead
            timers.push(setTimeout(openLid, 6000));
        };
        if (document.readyState === 'complete') idle(); else on(window, 'load', idle, { once: true });
    }

    /* ---------- Hero carousel: a coverflow of glass screens around the laptop ---------- */
    (function carousel() {
        const tracks = Array.prototype.map.call(dv.querySelectorAll('[data-sl-track]'), (tr) => ({
            off: parseInt(tr.getAttribute('data-sl-track'), 10) || 0, cards: tr.querySelectorAll('[data-sl-card]'),
        }));
        const ring = page.querySelectorAll('[data-cv]');
        const dots = dv.querySelectorAll('[data-sl-dot]'), stage = $('[data-h2-stage]');
        const n = tracks.length ? tracks[0].cards.length : ring.length;
        let idx = 0, paused = false, parkTimer = 0;
        if (n < 2) return;
        function place(first) {
            ring.forEach((c, k) => {
                let d = ((k - idx) % n + n) % n;
                if (d > n / 2) d -= n;
                const a = Math.abs(d), prev = c._d;
                const wrap = !first && prev !== undefined && Math.abs(d - prev) > 1;
                if (wrap) { c.classList.add('jump'); c.style.opacity = '0'; }
                c.style.setProperty('--d', d);
                c.style.setProperty('--a', a);
                c.style.setProperty('--s', d === 0 ? 0 : d > 0 ? 1 : -1);
                c.style.zIndex = 10 - a;
                c.setAttribute('aria-hidden', a === 0 || a > 2 ? 'true' : 'false');
                c.tabIndex = a === 1 ? 0 : -1;
                const op = a === 0 ? 0 : a === 1 ? 1 : a === 2 ? 0.5 : 0;
                if (wrap) { void c.offsetWidth; c.classList.remove('jump'); requestAnimationFrame(() => { c.style.opacity = op; }); }
                else c.style.opacity = op;
                c._d = d;
            });
        }
        function go(i, first) {
            idx = ((i % n) + n) % n;
            place(first);
            tracks.forEach((tr) => {
                const want = ((idx + tr.off) % n + n) % n;
                tr.cards.forEach((c, k) => {
                    const was = c.classList.contains('on');
                    c.classList.toggle('on', k === want);
                    c.classList.toggle('out', !first && was && k !== want);
                });
            });
            dots.forEach((d, k) => d.classList.toggle('on', k === idx));
            clearTimeout(parkTimer);
            parkTimer = setTimeout(() => {
                tracks.forEach((tr) => tr.cards.forEach((c) => { if (!c.classList.contains('on')) c.classList.remove('out'); }));
            }, 900);
        }
        go(0, true);
        if (stage) {
            on(stage, 'mouseenter', () => { paused = true; });
            on(stage, 'mouseleave', () => { paused = false; });
        }
        intervals.push(setInterval(() => {
            if (paused || document.hidden || hero.classList.contains('off') || hero.classList.contains('away')) return;
            go(idx + 1);
        }, reduce ? 6000 : 3800));
        offs.push(() => clearTimeout(parkTimer));
    })();

    // Pause the hero's looping animations while it is off screen
    let io = null;
    if (window.IntersectionObserver) {
        io = new IntersectionObserver((es) => hero.classList.toggle('off', !es[0].isIntersecting));
        io.observe(hero);
    }

    hero.classList.add('go');           // the headline and text come in at once
    mode();
    if (can3d()) load3d(); else openLid();
    on(window, 'scroll', () => { if (moving || three) schedule(); }, { passive: true });
    on(window, 'resize', () => { measure(); if (moving || three) kick(); else placeStatic(); });
    on(desktop, 'change', mode);
    // Sections arriving later (deferred data) change the page height: measure again (once per frame at most)
    let ro = null, mq = 0;
    if (window.ResizeObserver) {
        ro = new ResizeObserver(() => { if (!mq) mq = window.requestAnimationFrame(() => { mq = 0; if (stopped) return; measure(); if (moving || three) schedule(); }); });
        ro.observe(document.body);
    }

    return function stop() {
        stopped = true;
        cancelAnimationFrame(raf);
        cancelAnimationFrame(introRaf);
        cancelAnimationFrame(mq);
        if (idleId && window.cancelIdleCallback) window.cancelIdleCallback(idleId);
        timers.forEach(clearTimeout);
        intervals.forEach(clearInterval);
        offs.forEach((f) => f());
        if (io) io.disconnect();
        if (ro) ro.disconnect();
        if (three) { three.dispose(); three = null; }
        screenHome.appendChild(screenEl);
        dv.remove();
    };
}
