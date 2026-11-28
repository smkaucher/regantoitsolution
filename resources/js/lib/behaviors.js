/* The page behaviours of public/assets/js/site.js and home.js, for the React pages.
   Each init(root) works on the markup React rendered (same classes and data-attributes as the Blade pages, so the
   same CSS applies) and returns a cleanup function, so nothing keeps running or listening after Inertia moves to
   another page. Use them through useBehaviors() (resources/js/lib/useBehaviors.js). */

const reduce = () => window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const t = (k) => (window.t ? window.t(k) : k);

/** Collects cleanups; run() undoes everything in reverse order. */
function bag() {
    const fns = [];
    return {
        add(fn) { fns.push(fn); return fn; },
        on(target, type, fn, opts) { target.addEventListener(type, fn, opts); fns.push(() => target.removeEventListener(type, fn, opts)); },
        io(cb, opts, el) { const o = new IntersectionObserver(cb, opts); o.observe(el); fns.push(() => o.disconnect()); return o; },
        run() { while (fns.length) { try { fns.pop()(); } catch (e) { /* keep cleaning */ } } },
    };
}

/* ---------- Replay-in-view, count-up, typing, words reveal, fade-up (site.js initAnimations) ---------- */
export function initAnimations(root) {
    const b = bag();
    const rm = reduce();
    const hasIO = 'IntersectionObserver' in window;
    const setIn = (el, v) => {
        if (el.classList.contains('in') === v) return;
        el.classList.toggle('in', v);
        el.dispatchEvent(new CustomEvent(v ? 'replay:in' : 'replay:out'));
    };
    const watch = (el, th) => {
        if (!hasIO || rm) { setIn(el, true); return; }
        b.io((entries) => {
            const en = entries[0];
            if (en.intersectionRatio >= th) setIn(el, true);
            else if (!en.isIntersecting) setIn(el, false);
        }, { threshold: [0, th] }, el);
    };

    root.querySelectorAll('[data-replay]').forEach((el) => watch(el, parseFloat(el.dataset.threshold || '0.3')));

    root.querySelectorAll('[data-count]').forEach((el) => {
        const value = parseFloat(el.dataset.count), dec = parseInt(el.dataset.decimals || '0', 10);
        const suffix = el.dataset.suffix || '', dur = parseFloat(el.dataset.duration || '2') * 1000;
        const host = el.closest('[data-replay]') || el;
        let raf = 0;
        const paint = (v) => { el.textContent = v.toFixed(dec) + suffix; };
        const run = () => {
            cancelAnimationFrame(raf);
            if (rm) return paint(value);
            const t0 = performance.now();
            const step = (now) => {
                const p = Math.min((now - t0) / dur, 1), e = 1 - Math.pow(1 - p, 3);
                paint(value * e);
                if (p < 1) raf = requestAnimationFrame(step);
            };
            step(t0);
        };
        const out = () => { cancelAnimationFrame(raf); paint(0); };
        paint(rm ? value : 0);
        b.on(host, 'replay:in', run);
        b.on(host, 'replay:out', out);
        b.add(() => cancelAnimationFrame(raf));
        if (host.classList.contains('in')) run();
    });

    root.querySelectorAll('[data-typing]').forEach((el) => {
        let prompts;
        try { prompts = JSON.parse(el.dataset.typing); } catch (e) { return; }
        if (!prompts.length) return;
        const caret = document.createElement('i');
        if (el.dataset.caret) caret.className = el.dataset.caret;
        const txt = document.createTextNode('');
        el.textContent = '';
        el.appendChild(txt);
        el.appendChild(caret);
        if (rm) { txt.nodeValue = prompts[0]; return; }
        let i = 0, pos = 0, del = false, timer = 0;
        const tick = () => {
            const full = prompts[i];
            let d;
            if (!del) { pos++; txt.nodeValue = full.slice(0, pos); if (pos === full.length) { del = true; d = 1700; } else d = 65; }
            else { pos--; txt.nodeValue = full.slice(0, pos); if (pos === 0) { del = false; i = (i + 1) % prompts.length; d = 450; } else d = 28; }
            timer = setTimeout(tick, d);
        };
        tick();
        b.add(() => clearTimeout(timer));
    });

    root.querySelectorAll('[data-words]').forEach((el) => {
        const words = el.textContent.trim().split(/\s+/), step = parseFloat(el.dataset.stagger || '0.06');
        el.textContent = '';
        el.classList.add('rw');
        words.forEach((w, i) => {
            const s = document.createElement('span');
            s.className = 'rw-word';
            s.textContent = w;
            s.style.transitionDelay = (i * step) + 's';
            el.appendChild(s);
            if (i < words.length - 1) el.appendChild(document.createTextNode(' '));
        });
        if (!el.closest('[data-replay]')) watch(el, 0.3);
    });

    root.querySelectorAll('[data-fade]').forEach((el) => {
        if (el.hasAttribute('data-replay')) return;
        watch(el, 0.15);
    });
    root.querySelectorAll('[data-fade-stagger] > *').forEach((c, i) => { c.style.transitionDelay = (i * 0.08) + 's'; });

    return () => b.run();
}

/* ---------- Reviews 3D card deck (home.js initCarousels) ---------- */
export function initCarousels(root) {
    const b = bag();
    const rm = reduce();
    root.querySelectorAll('[data-carousel]').forEach((el) => {
        const cards = el.querySelectorAll('[data-card]'), dots = el.querySelectorAll('[data-dot]');
        const n = cards.length;
        let active = 0, paused = false, startX = null;
        if (!n) return;
        const render = () => {
            cards.forEach((card, i) => {
                let d = (((i - active) % n) + n) % n;
                if (d > n / 2) d -= n;
                const a = Math.abs(d);
                card.style.setProperty('--d', d);
                card.style.setProperty('--a', a);
                card.style.setProperty('--s', Math.sign(d));
                card.style.zIndex = 10 - a;
                card.style.opacity = a === 0 ? 1 : a === 1 ? 0.65 : a === 2 ? 0.3 : 0;
                card.style.pointerEvents = a > 2 ? 'none' : 'auto';
                card.classList.toggle('on', d === 0);
                if (d === 0) card.removeAttribute('aria-hidden'); else card.setAttribute('aria-hidden', 'true');
            });
            dots.forEach((dot, i) => dot.classList.toggle('on', i === active));
        };
        const go = (i) => { active = ((i % n) + n) % n; render(); };
        cards.forEach((card, i) => b.on(card, 'click', () => { if (i !== active) go(i); }));
        dots.forEach((dot, i) => b.on(dot, 'click', () => go(i)));
        const prev = el.querySelector('[data-prev]'), next = el.querySelector('[data-next]');
        if (prev) b.on(prev, 'click', () => go(active - 1));
        if (next) b.on(next, 'click', () => go(active + 1));
        b.on(el, 'keydown', (e) => { if (e.key === 'ArrowLeft') go(active - 1); if (e.key === 'ArrowRight') go(active + 1); });
        ['mouseenter', 'focusin'].forEach((ev) => b.on(el, ev, () => { paused = true; }));
        ['mouseleave', 'focusout'].forEach((ev) => b.on(el, ev, () => { paused = false; }));
        b.on(el, 'pointerdown', (e) => { startX = e.clientX; });
        b.on(el, 'pointerup', (e) => {
            if (startX === null) return;
            const dx = e.clientX - startX;
            startX = null;
            if (Math.abs(dx) > 45) go(active + (dx < 0 ? 1 : -1));
        });
        render();
        if (!rm) { const id = setInterval(() => { if (!paused) go(active + 1); }, 4500); b.add(() => clearInterval(id)); }
    });
    return () => b.run();
}

/* ---------- Reviews "Write a review" dialog (home.js initReviewForms) ---------- */
function cookie(name) {
    const m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
    return m ? decodeURIComponent(m[1]) : '';
}
export function initReviewForms(root) {
    const b = bag();
    root.querySelectorAll('[data-review-dialog]').forEach((dlg) => {
        const sec = dlg.closest('section') || root;
        const form = dlg.querySelector('[data-review-form]');
        const status = form.querySelector('[data-review-status]');
        const send = form.querySelector('[type=submit]');
        const count = form.querySelector('[data-review-count]');
        const quote = form.elements.quote;
        const say = (msg, kind) => { status.textContent = msg || ''; status.className = 'rv-status' + (kind ? ' ' + kind : ''); };
        const clearErrors = () => {
            form.querySelectorAll('[data-err]').forEach((e) => { e.textContent = ''; });
            form.querySelectorAll('.has-err').forEach((e) => e.classList.remove('has-err'));
        };
        const showErrors = (errors) => {
            Object.keys(errors).forEach((k) => {
                const e = form.querySelector('[data-err="' + k + '"]');
                if (!e) return;
                e.textContent = [].concat(errors[k])[0];
                e.parentNode.classList.add('has-err');
            });
            const first = form.querySelector('.has-err input, .has-err textarea');
            if (first) first.focus();
        };
        const open = () => {
            form.classList.remove('sent');
            say('');
            clearErrors();
            if (dlg.showModal) dlg.showModal(); else dlg.setAttribute('open', '');
            form.elements.name.focus();
        };
        const close = () => { if (dlg.close) dlg.close(); else dlg.removeAttribute('open'); };
        sec.querySelectorAll('[data-review-open]').forEach((x) => b.on(x, 'click', open));
        dlg.querySelectorAll('[data-review-close]').forEach((x) => b.on(x, 'click', close));
        b.on(dlg, 'click', (e) => { if (e.target === dlg) close(); });
        b.on(quote, 'input', () => { count.textContent = quote.value.length; });
        b.add(() => { if (dlg.open && dlg.close) dlg.close(); });
        b.on(form, 'submit', (e) => {
            e.preventDefault();
            clearErrors();
            const errs = {}, text = quote.value.trim(), file = form.elements.photo.files[0];
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
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-XSRF-TOKEN': cookie('XSRF-TOKEN') },
            })
                .then((r) => r.json().catch(() => ({})).then((d) => ({ code: r.status, data: d })))
                .then((res) => {
                    if (res.code === 200) {
                        form.reset();
                        count.textContent = '0';
                        form.classList.add('sent');
                        say(res.data.message || t('Thank you! Your review will show on the site after our team checks it.'), 'ok');
                    } else if (res.code === 422 && res.data.errors) {
                        showErrors(res.data.errors);
                        say(res.data.message, 'err');
                    } else if (res.code === 429) say(t('Too many tries. Please wait a few minutes and try again.'), 'err');
                    else if (res.code === 419) say(t('This page was open too long. Please reload the page and try again.'), 'err');
                    else say(t('Could not send your review. Please try again.'), 'err');
                })
                .catch(() => say(t('Could not send your review. Please check your internet and try again.'), 'err'))
                .then(() => { send.disabled = false; });
        });
    });
    return () => b.run();
}

/* ---------- Home "Our Services": pinned, scroll-driven steps (home.js initServiceScroll) ---------- */
export function initServiceScroll(root) {
    const b = bag();
    const desktop = window.matchMedia('(min-width: 901px)');
    root.querySelectorAll('[data-svs]').forEach((sec) => {
        const track = sec.querySelector('.svs-track');
        const slabs = sec.querySelectorAll('[data-svs-slab]');
        const panels = sec.querySelectorAll('[data-svs-panel]');
        const steps = sec.querySelectorAll('[data-svs-step]');
        const cap = sec.querySelector('[data-svs-cap]');
        const n = slabs.length;
        let active = -1, raf = 0;
        if (!n) return;
        const progress = () => {
            const r = track.getBoundingClientRect();
            const span = r.height - window.innerHeight;
            return span > 0 ? Math.min(Math.max(-r.top / span, 0), 1) : 0;
        };
        const setActive = (i) => {
            if (i === active) return;
            active = i;
            slabs.forEach((s, k) => { s.classList.toggle('on', k === i); s.classList.toggle('past', k < i); });
            panels.forEach((p, k) => p.classList.toggle('on', k === i));
            steps.forEach((x, k) => { x.classList.toggle('on', k === i); x.setAttribute('aria-selected', k === i ? 'true' : 'false'); });
            if (cap) {
                const title = slabs[i].querySelector('.svs-tt');
                cap.textContent = (i + 1 < 10 ? '0' : '') + (i + 1) + ' · ' + (title ? title.textContent : '');
            }
        };
        const render = () => {
            raf = 0;
            if (!desktop.matches) return;
            const pp = progress();
            sec.style.setProperty('--pp', pp.toFixed(4));
            setActive(Math.min(n - 1, Math.floor(pp * n)));
        };
        const queue = () => { if (!raf) raf = requestAnimationFrame(render); };
        steps.forEach((x, i) => b.on(x, 'click', () => {
            const r = track.getBoundingClientRect();
            const span = r.height - window.innerHeight;
            window.scrollTo({ top: window.scrollY + r.top + span * (i + 0.5) / n, behavior: 'smooth' });
        }));
        b.on(window, 'scroll', queue, { passive: true });
        b.on(window, 'resize', queue);
        b.on(desktop, 'change', queue);
        b.add(() => cancelAnimationFrame(raf));
        render();
    });
    return () => b.run();
}

/* ---------- Recent work: filter tabs and card order (site.js initSections, [data-wk2]) ---------- */
export function initWorkFilter(root) {
    const b = bag();
    const GROW = [2, 1, 1.4, 1.6, 1, 2, 1.5, 1.1];
    root.querySelectorAll('[data-wk2]').forEach((sec) => {
        const tabs = sec.querySelectorAll('.wk2-tabs button');
        const cards = sec.querySelectorAll('.wk2-card');
        const grid = sec.querySelector('.wk2-grid');
        let ORDER = {};
        try { ORDER = JSON.parse(sec.dataset.order || '{}'); } catch (e) { ORDER = {}; }
        const show = (tag) => {
            grid.classList.toggle('is-all', tag === 'All');
            grid.classList.toggle('is-brand', tag === 'Graphics & Branding');
            const list = ORDER[tag] || [];
            let n = 0;
            cards.forEach((c) => {
                const ok = tag === 'All' || JSON.parse(c.dataset.tags || '[]').indexOf(tag) > -1;
                c.hidden = !ok;
                if (!ok) return;
                const r = list.indexOf(c.dataset.slug);
                c.style.order = r > -1 ? r : 100 + parseInt(c.dataset.i, 10);
                c.style.setProperty('--g', GROW[n % GROW.length]);
                c.style.setProperty('--n', Math.min(n++, 10));
                c.style.animation = 'none';
                void c.offsetWidth;
                c.style.animation = '';
            });
            const limit = parseInt(grid.dataset.limit || '0', 10);
            if (limit) {
                const shown = Array.prototype.filter.call(cards, (c) => !c.hidden)
                    .sort((a, z) => parseInt(a.style.order, 10) - parseInt(z.style.order, 10));
                shown.forEach((c, k) => {
                    if (k >= limit) { c.hidden = true; return; }
                    c.dataset.slot = k;
                    c.style.setProperty('--n', k);
                });
                grid.dataset.cards = Math.min(shown.length, limit);
            }
        };
        tabs.forEach((x) => b.on(x, 'click', () => {
            tabs.forEach((y) => { y.classList.toggle('on', y === x); y.setAttribute('aria-selected', y === x ? 'true' : 'false'); });
            show(x.dataset.tag);
        }));
        if (tabs[0]) show(tabs[0].dataset.tag);
    });
    return () => b.run();
}
