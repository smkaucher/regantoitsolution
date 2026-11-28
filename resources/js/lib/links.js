/* Page changes without a reload: a click on an ordinary link to another React page of this site is sent through
   Inertia. The pages keep plain <a href> markup (like the Blade pages), so this one listener covers every link,
   also those inside HTML the server sends (the service pop-up). Everything else is left to the browser:
   other sites, new tabs, downloads, same-page #anchors, the language switch, the admin panel, files. */
import { router } from '@inertiajs/react';

// Pages that are still Blade (a full page load). Remove a path from here once its page is built in React.
export const BLADE_PATHS = [/^\/about$/, /^\/services(\/.*)?$/, /^\/projects(\/.*)?$/, /^\/team$/, /^\/contact$/, /^\/home2$/];
const NEVER = [/^\/admin(\/|$)/, /^\/lang\//, /^\/_\//, /^\/storage\//, /^\/assets\//, /^\/build\//, /\.(xml|txt|pdf|zip|png|jpe?g|webp|svg)$/i];

export function initLinks() {
    const onClick = (e) => {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const a = e.target.closest && e.target.closest('a[href]');
        if (!a || a.target && a.target !== '_self' || a.hasAttribute('download') || a.hasAttribute('data-svc-pop') || a.hasAttribute('data-ai-open')) return;
        const url = new URL(a.href, window.location.href);
        if (url.origin !== window.location.origin) return;
        if ([...NEVER, ...BLADE_PATHS].some((re) => re.test(url.pathname))) return;
        // the same page with only a #section: let the browser scroll there
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
        e.preventDefault();
        router.visit(url.pathname + url.search + url.hash);
    };
    document.addEventListener('click', onClick);
    return () => document.removeEventListener('click', onClick);
}
