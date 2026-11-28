/* Service pop-up (site.js): any link marked data-svc-pop opens that service in a compact dialog. The dialog's
   content is the server's Blade partial (?partial=1), so it is the same as on the Blade pages and already in the
   visitor's language. The pop-up puts the service's address in the address bar; going Back closes it. Its own
   Back is handled before Inertia's (capture listener), so Inertia never reloads the page underneath.
   Returns a cleanup. */
import { translateTree } from './translate';

export function initServicePop() {
    const t = (k) => (window.t ? window.t(k) : k);
    let box = null, content = null, closeBtn = null, lastFocus = null, pushed = false, token = 0;
    const cache = {};

    const build = () => {
        if (box) return;
        box = document.createElement('div');
        box.className = 'sp';
        box.hidden = true;
        box.innerHTML = '<div class="sp-back" data-sp-close></div>'
            + '<div class="sp-box" role="dialog" aria-modal="true" aria-labelledby="spTitle">'
            + '<button type="button" class="sp-x" data-sp-close aria-label="Close">&times;</button>'
            + '<div class="sp-content"></div></div>';
        document.body.appendChild(box);
        content = box.querySelector('.sp-content');
        closeBtn = box.querySelector('.sp-x');
        translateTree(box);
        box.addEventListener('click', (e) => { if (e.target.closest('[data-sp-close]')) close(); });
    };
    const show = (html) => {
        content.innerHTML = html;
        const sc = content.querySelector('.sp-scroll');
        if (sc) sc.scrollTop = 0;
        box.hidden = false;
        document.documentElement.classList.add('sp-lock');
        closeBtn.focus();
    };
    const open = (url) => {
        build();
        lastFocus = document.activeElement;
        const my = ++token;
        // keep Inertia's page state in the new history entry, so Inertia still knows the page after the pop-up
        if (!pushed) { window.history.pushState({ ...(window.history.state || {}), sp: 1 }, '', url); pushed = true; }
        else window.history.replaceState({ ...(window.history.state || {}), sp: 1 }, '', url);
        if (cache[url]) { show(cache[url]); return; }
        content.innerHTML = '<div class="sp-loading">' + t('Loading…') + '</div>';
        box.hidden = false;
        document.documentElement.classList.add('sp-lock');
        fetch(url + (url.indexOf('?') > -1 ? '&' : '?') + 'partial=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((r) => { if (!r.ok) throw new Error(r.status); return r.text(); })
            .then((html) => { cache[url] = html; if (my === token && !box.hidden) show(html); })
            .catch(() => { if (my === token) window.location.href = url; });
    };
    const hide = () => {
        if (!box || box.hidden) return;
        box.hidden = true;
        document.documentElement.classList.remove('sp-lock');
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    };
    function close() {
        if (pushed) window.history.back(); else hide();
    }

    const onClick = (e) => {
        const a = e.target.closest && e.target.closest('a[data-svc-pop]');
        if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        e.stopPropagation();
        open(a.href);
    };
    const onPop = (e) => {
        if (pushed || (box && !box.hidden)) {
            pushed = false;
            hide();
            e.stopImmediatePropagation(); // ours, not a page change: Inertia must not handle it
        }
    };
    const onKey = (e) => { if (e.key === 'Escape' && box && !box.hidden) close(); };

    document.addEventListener('click', onClick, true);
    window.addEventListener('popstate', onPop, true);
    window.addEventListener('keydown', onKey);
    return () => {
        document.removeEventListener('click', onClick, true);
        window.removeEventListener('popstate', onPop, true);
        window.removeEventListener('keydown', onKey);
        if (box) { box.remove(); document.documentElement.classList.remove('sp-lock'); }
    };
}
