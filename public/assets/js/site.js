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
  /* ---------- Header: mobile menu; turns solid once the page is scrolled (transparent over the home hero) ---------- */
  var hd = document.getElementById('rgHd');
  if (hd) {
    var menu = document.getElementById('rgMenu');
    if (menu) menu.addEventListener('click', function () {
      var open = !hd.classList.contains('menu-open');
      hd.classList.toggle('menu-open', open);
      menu.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Wide screens: if the links don't fit between the logo and the buttons, use the menu button (.rg-tight).
    // Measured with the class off, so the bar can go back to the full row when there is room again.
    var nav = document.getElementById('rgNav'), wide = window.matchMedia('(min-width:1181px)');
    var fit = function () {
      if (!nav) return;
      if (!wide.matches) { hd.classList.remove('rg-tight'); return; }
      var wasOpen = hd.classList.contains('menu-open');
      hd.classList.remove('rg-tight');
      var tight = nav.scrollWidth > nav.clientWidth + 1;
      hd.classList.toggle('rg-tight', tight);
      if (!tight && wasOpen) { hd.classList.remove('menu-open'); if (menu) menu.setAttribute('aria-expanded', 'false'); }
    };
    fit();
    window.addEventListener('resize', fit);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(fit);
    window.addEventListener('load', fit);

    var onScroll = function () { hd.classList.toggle('stuck', window.scrollY > 10); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

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
  function setIn(el, v) {
    if (el.classList.contains('in') === v) return;
    el.classList.toggle('in', v);
    el.dispatchEvent(new CustomEvent(v ? 'replay:in' : 'replay:out'));
  }
  function initAnimations(root) {
  var replayEls = root.querySelectorAll('[data-replay]');
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
  root.querySelectorAll('[data-count]').forEach(function (el) {
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
  root.querySelectorAll('[data-typing]').forEach(function (el) {
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
  root.querySelectorAll('[data-words]').forEach(function (el) {
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
  root.querySelectorAll('[data-words][data-replay]:not(.in)').forEach(function (el) {
    if (!('IntersectionObserver' in window) || reduce) { el.classList.add('in'); return; }
    new IntersectionObserver(function (entries) {
      var en = entries[0];
      if (en.intersectionRatio >= 0.3) setIn(el, true); else if (!en.isIntersecting) setIn(el, false);
    }, { threshold: [0, 0.3] }).observe(el);
  });

  /* ---------- Fade-up blocks: <div data-fade> (stagger children with data-fade-stagger) ---------- */
  root.querySelectorAll('[data-fade]').forEach(function (el) {
    if (el.hasAttribute('data-replay')) return;
    el.setAttribute('data-replay', '');
    if (!('IntersectionObserver' in window) || reduce) { el.classList.add('in'); return; }
    new IntersectionObserver(function (entries) {
      var en = entries[0];
      if (en.intersectionRatio >= 0.15) setIn(el, true); else if (!en.isIntersecting) setIn(el, false);
    }, { threshold: [0, 0.15] }).observe(el);
  });
  root.querySelectorAll('[data-fade-stagger] > *').forEach(function (c, i) { c.style.transitionDelay = (i * 0.08) + 's'; });
  }
  initAnimations(document);
  window.initAnimations = initAnimations;
})();

/* ---------- Service pop-up: any link marked data-svc-pop opens that service in a compact dialog ---------- */
(function () {
  var box = null, content = null, closeBtn = null, lastFocus = null, pushed = false, base = null, cache = {}, token = 0;

  function build() {
    if (box) return;
    box = document.createElement('div');
    box.className = 'sp';
    box.hidden = true;
    box.innerHTML = '<div class="sp-back" data-sp-close></div>' +
      '<div class="sp-box" role="dialog" aria-modal="true" aria-labelledby="spTitle">' +
      '<button type="button" class="sp-x" data-sp-close aria-label="Close">&times;</button>' +
      '<div class="sp-content"></div></div>';
    document.body.appendChild(box);
    content = box.querySelector('.sp-content');
    closeBtn = box.querySelector('.sp-x');
    box.addEventListener('click', function (e) { if (e.target.closest('[data-sp-close]')) close(); });
  }

  function show(html) {
    content.innerHTML = html;
    var sc = content.querySelector('.sp-scroll');
    if (sc) sc.scrollTop = 0;
    box.hidden = false;
    document.documentElement.classList.add('sp-lock');
    closeBtn.focus();
  }

  function open(url, initial) {
    build();
    lastFocus = document.activeElement;
    var my = ++token;
    if (initial) { /* the address bar already shows this service */ }
    else if (!pushed) { history.pushState({ sp: 1 }, '', url); pushed = true; } else { history.replaceState({ sp: 1 }, '', url); }
    if (cache[url]) { show(cache[url]); return; }
    content.innerHTML = '<div class="sp-loading">' + t('Loading…') + '</div>';
    box.hidden = false;
    document.documentElement.classList.add('sp-lock');
    fetch(url + (url.indexOf('?') > -1 ? '&' : '?') + 'partial=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(function (html) { cache[url] = html; if (my === token && !box.hidden) show(html); })
      .catch(function () { if (my === token) window.location.href = url; }); // no pop-up possible: open the page itself
  }

  function hide() {
    if (!box || box.hidden) return;
    box.hidden = true;
    document.documentElement.classList.remove('sp-lock');
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  }

  function close() {
    if (pushed) { pushed = false; history.back(); }
    else { hide(); if (base) { history.replaceState({}, '', base); base = null; } }
  }

  document.addEventListener('click', function (e) {
    var a = e.target.closest && e.target.closest('a[data-svc-pop]');
    if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    e.preventDefault();
    open(a.href);
  });
  var pre = document.querySelector('[data-svc-open]');
  if (pre) { base = pre.getAttribute('data-svc-base'); open(pre.getAttribute('data-svc-open'), true); }
  window.addEventListener('popstate', function () { pushed = false; hide(); });
  window.addEventListener('keydown', function (e) { if (e.key === 'Escape' && box && !box.hidden) close(); });
})();

/* ---------- Lazy sections: <div data-lazy="/url"> is filled with that HTML shortly before it scrolls into view ---------- */
(function () {
  var holders = document.querySelectorAll('[data-lazy]');
  if (!holders.length) return;

  function run(el) {
    // Scripts inserted with innerHTML do not run, so each one is recreated.
    el.querySelectorAll('script').forEach(function (old) {
      var s = document.createElement('script');
      s.text = old.textContent;
      old.parentNode.replaceChild(s, old);
    });
    if (window.initSections) window.initSections(el);
    if (window.initAnimations) window.initAnimations(el);
    if (window.initCarousels) window.initCarousels(el);
    if (window.initReviewForms) window.initReviewForms(el);
    if (window.initServiceScroll) window.initServiceScroll(el);
  }

  function load(el) {
    var url = el.getAttribute('data-lazy');
    if (!url) return;
    el.removeAttribute('data-lazy');
    el.classList.add('is-loading');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
      .then(function (html) {
        el.innerHTML = html;
        el.classList.remove('is-loading', 'lazy-ph');
        el.style.minHeight = '';
        run(el);
      })
      .catch(function () {
        el.classList.remove('is-loading');
        el.innerHTML = '<p class="lazy-fail"><button type="button" class="btn btn-sm">' + t('Tap to load this section') + '</button></p>';
        el.querySelector('button').addEventListener('click', function () { el.setAttribute('data-lazy', url); el.innerHTML = ''; load(el); });
      });
  }

  if (!('IntersectionObserver' in window)) { holders.forEach(load); return; }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) { if (e.isIntersecting) { io.unobserve(e.target); load(e.target); } });
  }, { rootMargin: '500px 0px' });
  holders.forEach(function (el) { io.observe(el); });

  // A link such as #work jumps to a section that is not loaded yet: load it right away.
  window.addEventListener('hashchange', function () {
    var target = document.getElementById(location.hash.slice(1));
    if (target && target.hasAttribute('data-lazy')) { io.unobserve(target); load(target); }
  });
})();

/* ---------- Section widgets: service tabs ([data-sv2]) and work filter ([data-wk2]). Re-run for sections that load later: initSections(root). ---------- */
(function () {
  function initSections(root) {
    // Services: tabs, one card group per category
    root.querySelectorAll('[data-sv2]').forEach(function (sec) {
      if (sec.dataset.ready) return;
      sec.dataset.ready = '1';
      var tabs = sec.querySelectorAll('.wk2-tabs button');
      var grid = sec.querySelector('.sv2-grid');
      var GROW = [2, 1, 1.4, 1.6, 1, 2, 1.5, 1.1]; // varied widths; every row still fills the full width
      // The cards of a category are fetched the first time its tab is opened.
      function ensure(tag) {
        if (tag === 'all' || grid.querySelector('.sv2-card[data-tag="' + tag + '"]')) return Promise.resolve();
        grid.classList.add('is-busy');
        return fetch(sec.dataset.src + '/' + encodeURIComponent(tag) + (sec.dataset.v ? '?v=' + sec.dataset.v : ''), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
          .then(function (r) { if (!r.ok) throw new Error(r.status); return r.text(); })
          .then(function (html) { grid.insertAdjacentHTML('beforeend', html); })
          .finally(function () { grid.classList.remove('is-busy'); });
      }
      function show(tag) {
        var n = 0;
        grid.querySelectorAll('.sv2-card').forEach(function (c) {
          var ok = c.dataset.tag === tag;
          c.hidden = !ok;
          if (!ok) return;
          c.style.setProperty('--g', GROW[n % GROW.length]);
          c.style.setProperty('--n', Math.min(n++, 10));
          c.style.animation = 'none'; void c.offsetWidth; c.style.animation = '';
        });
      }
      tabs.forEach(function (b) {
        b.addEventListener('click', function () {
          tabs.forEach(function (x) { x.classList.toggle('on', x === b); x.setAttribute('aria-selected', x === b ? 'true' : 'false'); });
          ensure(b.dataset.tag).then(function () { show(b.dataset.tag); })
            .catch(function () { window.location.href = '/services/' + b.dataset.tag; }); // no cards: open the category page
        });
      });
      // Open on the category named in the link (/services#marketing), else on "All".
      var start = Array.prototype.find.call(tabs, function (t) { return '#' + t.dataset.tag === location.hash; }) || tabs[0];
      if (start) start.click();
    });

    // Recent work: filter tabs and card order
    root.querySelectorAll('[data-wk2]').forEach(function (sec) {
      if (sec.dataset.ready) return;
      sec.dataset.ready = '1';
      var tabs = sec.querySelectorAll('.wk2-tabs button');
      var cards = sec.querySelectorAll('.wk2-card');
      var grid = sec.querySelector('.wk2-grid');
      var ORDER = {};
      try { ORDER = JSON.parse(sec.dataset.order || '{}'); } catch (e) { ORDER = {}; }
      var GROW = [2, 1, 1.4, 1.6, 1, 2, 1.5, 1.1];
      function show(tag) {
        grid.classList.toggle('is-all', tag === 'All');
        grid.classList.toggle('is-brand', tag === 'Graphics & Branding');
        var list = ORDER[tag] || [];
        var n = 0;
        cards.forEach(function (c) {
          var ok = tag === 'All' || JSON.parse(c.dataset.tags || '[]').indexOf(tag) > -1;
          c.hidden = !ok;
          if (!ok) return;
          var r = list.indexOf(c.dataset.slug);
          c.style.order = r > -1 ? r : 100 + parseInt(c.dataset.i, 10);
          c.style.setProperty('--g', GROW[n % GROW.length]);
          c.style.setProperty('--n', Math.min(n++, 10));
          c.style.animation = 'none'; void c.offsetWidth; c.style.animation = '';
        });
        // Home bento grid (data-limit): number the shown cards in their on-screen order as data-slot, keep only the
        // first `limit`, and tell the grid how many it holds (data-cards; not data-count, which the count-up animation uses) so CSS picks the 3-row layout for that count.
        var limit = parseInt(grid.dataset.limit || '0', 10);
        if (limit) {
          var shown = Array.prototype.filter.call(cards, function (c) { return !c.hidden; })
            .sort(function (a, b) { return parseInt(a.style.order, 10) - parseInt(b.style.order, 10); });
          shown.forEach(function (c, k) {
            if (k >= limit) { c.hidden = true; return; }
            c.dataset.slot = k;
            c.style.setProperty('--n', k);
          });
          grid.dataset.cards = Math.min(shown.length, limit);
        }
      }
      tabs.forEach(function (b) {
        b.addEventListener('click', function () {
          tabs.forEach(function (x) { x.classList.toggle('on', x === b); x.setAttribute('aria-selected', x === b ? 'true' : 'false'); });
          show(b.dataset.tag);
        });
      });
      if (tabs[0]) show(tabs[0].dataset.tag);
    });
  }
  window.initSections = initSections;
  initSections(document);
})();
