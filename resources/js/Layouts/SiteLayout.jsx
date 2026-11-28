/* The frame of every React public page: the same top bar, navbar, footer and chat buttons as
   resources/views/layouts/app.blade.php. It stays mounted while Inertia changes pages, so the chat keeps its
   state; page content goes in <main>. */
import { useEffect, useLayoutEffect, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import Icon from '../Components/Icon';
import { initAiChat } from '../lib/aiChat';
import { initServicePop } from '../lib/servicePop';
import { initLinks } from '../lib/links';
import { watchTranslations } from '../lib/translate';

const url = (p) => p; // same-site paths; kept as a function so links read like the Blade url() calls

function Announce({ site }) {
    return (
        <div className="rg-tb">
            <div className="rg-tb-in">{'● ' + (site.site_name || 'Reganto IT Solution').toUpperCase() + ' — DIGITAL EXPERIENCES BUILT FOR WHAT’S NEXT '}<a href={url('/contact')}>LET’S TALK ↗</a></div>
        </div>
    );
}

function Navbar({ site, navServices, locale, path }) {
    const hd = useRef(null), nav = useRef(null), menu = useRef(null);
    const bn = locale === 'bn';
    const over = path === '/' ? 'rg-hd--over rg-hd--h2' : '';

    useEffect(() => {
        const h = hd.current, n = nav.current, m = menu.current;
        const wide = window.matchMedia('(min-width:1181px)');
        const onMenu = () => {
            const open = !h.classList.contains('menu-open');
            h.classList.toggle('menu-open', open);
            m.setAttribute('aria-expanded', open ? 'true' : 'false');
        };
        // Wide screens: if the links don't fit between the logo and the buttons, use the menu button (.rg-tight).
        const fit = () => {
            if (!wide.matches) { h.classList.remove('rg-tight'); return; }
            const wasOpen = h.classList.contains('menu-open');
            h.classList.remove('rg-tight');
            const tight = n.scrollWidth > n.clientWidth + 1;
            h.classList.toggle('rg-tight', tight);
            if (!tight && wasOpen) { h.classList.remove('menu-open'); m.setAttribute('aria-expanded', 'false'); }
        };
        const onScroll = () => h.classList.toggle('stuck', window.scrollY > 10);
        m.addEventListener('click', onMenu);
        window.addEventListener('resize', fit);
        window.addEventListener('load', fit);
        window.addEventListener('scroll', onScroll, { passive: true });
        fit();
        onScroll();
        return () => {
            m.removeEventListener('click', onMenu);
            window.removeEventListener('resize', fit);
            window.removeEventListener('load', fit);
            window.removeEventListener('scroll', onScroll);
        };
    }, [navServices.length]);

    // a page change closes the mobile menu
    useEffect(() => {
        hd.current.classList.remove('menu-open');
        menu.current.setAttribute('aria-expanded', 'false');
    }, [path]);

    const on = (test) => (test ? ' on' : '');
    return (
        <header className={'rg-hd ' + over} id="rgHd" ref={hd}>
            <div className="rg-in">
                <a className="rg-logo" href={url('/')}>
                    <img src="/assets/img/logo.png" alt="Reganto IT Solution" width="300" height="100" />
                </a>
                <nav className="rg-nav" id="rgNav" aria-label="Main" ref={nav}>
                    {navServices.map((s) => (
                        <a key={s.slug} className={'rg-lk' + on(path.startsWith('/services/' + s.slug))} href={url('/services/' + s.slug)}>{s.title}</a>
                    ))}
                    <a className={'rg-lk rg-lk--blue' + on(path === '/about')} href={url('/about')}>About Us</a>
                    <a className={'rg-lk rg-lk--blue' + on(path === '/team')} href={url('/team')}>Team</a>
                </nav>
                <div className="rg-act">
                    {/* a full page load: the language is chosen on the server */}
                    <a className="rg-lang" href={'/lang/' + (bn ? 'en' : 'bn')} aria-label="Switch language" title={bn ? 'Switch to English' : 'বাংলায় দেখুন'} hrefLang={bn ? 'en' : 'bn'}>{bn ? 'EN' : 'বাংলা'}</a>
                    <a className="rg-quote" href={url('/contact') + '#brief'}>Start a project <Icon name="arrow-circle" size="16" /></a>
                    <button type="button" className="rg-menu" id="rgMenu" ref={menu} aria-label="Menu" aria-expanded="false" aria-controls="rgNav"><span></span></button>
                </div>
            </div>
        </header>
    );
}

function Footer({ site, navServices, year }) {
    const soc = [['Facebook', 'facebook', site.facebook], ['Instagram', 'instagram', site.instagram], ['YouTube', 'youtube', site.youtube],
        ['TikTok', 'tiktok', site.tiktok], ['LinkedIn', 'linkedin', site.linkedin]];
    const tel = (site.phone || '').replace(/[^\d+]/g, '');
    return (
        <footer className="closing" id="contact">
            <div className="closing-top">
                <div className="closing-copy">
                    <h2>Let’s Build Something Bold — Together</h2>
                    <p>Book a FREE 15-minute strategy call with our expert team in the USA and globally to get personalized guidance in minutes.</p>
                    <div className="closing-actions">
                        <a className="btn" href={site.book_call_url || url('/contact')}>Book Your FREE Strategy Call <Icon name="arrow-right" /></a>
                        <a className="btn ghost-l" href={url('/services')}>View Services <Icon name="layers" /></a>
                    </div>
                    <div className="closing-pills">
                        <span><Icon name="shield" size="15" /> Privacy-first</span>
                        <span><Icon name="sparkle" size="15" /> Modern UI</span>
                        <span><Icon name="rocket" size="15" /> Fast Delivery</span>
                    </div>
                </div>
                <aside className="expert">
                    <div className="expert-head"><i><Icon name="star" size="20" /></i><div><b>Talk to an Expert</b><small>Response within 30 minutes</small></div></div>
                    <a href={'mailto:' + (site.email || '')}><Icon name="mail" /> Email Us</a>
                    <a href={'tel:' + tel}><Icon name="phone" /> Call Now</a>
                    <a href={site.whatsapp || '#'}><Icon name="whatsapp" /> WhatsApp</a>
                    <p className="expert-live"><i></i> Available: 24/7</p>
                </aside>
            </div>
            <div className="closing-cols">
                <div className="closing-brand">
                    <img src="/assets/img/logo.png" alt="Reganto IT Solution" />
                    <b>Design • Development • Growth</b>
                    <p>We craft clean UI/UX and high-performing websites for startups and businesses across United States and beyond.</p>
                    <div className="closing-soc">
                        {soc.map(([n, ic, href]) => href && (
                            <a key={ic} href={href} aria-label={n} title={n} target="_blank" rel="noreferrer"><Icon name={ic} /></a>
                        ))}
                    </div>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <a href={url('/')}>Home</a>
                    <a href={url('/projects')}>Projects</a>
                    <a href={url('/about')}>About</a>
                </div>
                <div>
                    <h4>Services</h4>
                    {navServices.map((s) => <a key={s.slug} href={url('/services/' + s.slug)}>{s.title}</a>)}
                </div>
                <div>
                    <h4>Others</h4>
                    <a href={url('/contact')}>Book a Service</a>
                    <a href={'mailto:' + (site.email || '')}>{site.email || ''}</a>
                    <a href={'tel:' + tel}>{site.phone || ''}</a>
                    <a href={site.map_url} target="_blank" rel="noreferrer">{site.address || site.address_fallback}</a>
                </div>
            </div>
            <div className="closing-bar"><small>{'© ' + year + ' ' + (site.footer_text || 'Reganto IT Solution. All Rights Reserved.')}</small><a href={url('/contact')}>Contact</a></div>
        </footer>
    );
}

function Chat({ site }) {
    const [open, setOpen] = useState(false);
    const [shown, setShown] = useState(false); // the panel stays in the page for its closing fade
    useEffect(() => {
        if (open) { setShown(true); return undefined; }
        const id = setTimeout(() => setShown(false), 220);
        return () => clearTimeout(id);
    }, [open]);
    const [lit, setLit] = useState(false);
    useEffect(() => {
        if (!open) { setLit(false); return undefined; }
        const id = requestAnimationFrame(() => setLit(true));
        return () => cancelAnimationFrame(id);
    }, [open]);
    useEffect(() => {
        const onKey = (e) => { if (e.key === 'Escape') setOpen(false); };
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
    }, []);
    return (
        <div className="chat" id="chat">
            <div className={'chat-panel' + (lit ? ' show' : '')} id="chatPanel" role="dialog" aria-label="Chat with our team" hidden={!shown}>
                <b>Fast Reply</b>
                <p>Choose an option to chat with us.</p>
                <a href="#" data-ai-open onClick={() => setOpen(false)}><b>Reganto AI</b> Instant answers, 24/7<small>Ask about services, projects and booking</small></a>
                <a href={site.facebook || '#'} target="_blank" rel="noreferrer"><b>Messenger</b> {site.site_name || ''}<small>Typically replies in ~30 minutes</small></a>
                <a href={site.whatsapp || '#'} target="_blank" rel="noreferrer"><b>WhatsApp</b> {site.phone || ''}</a>
            </div>
            <button className={'chat-fab' + (open ? ' open' : '')} id="chatFab" type="button" aria-label={open ? 'Close live chat' : 'Open live chat'} aria-expanded={open ? 'true' : 'false'} onClick={() => setOpen(!open)}>
                <span className="chat-fab-ico"><span id="chatIco">{open ? '✕' : '💬'}</span></span>
                <span className="chat-fab-txt"><b>Live Chat</b><small>AI · WhatsApp · Messenger</small></span>
            </button>
        </div>
    );
}

function AiChat({ chatUrl }) {
    const ref = useRef(null);
    useEffect(() => initAiChat(ref.current, chatUrl), [chatUrl]);
    return (
        <div className="aic" id="aic" hidden ref={ref}>
            <div className="aic-back" data-aic-close></div>
            <section className="aic-box" role="dialog" aria-modal="true" aria-label="Chat with Reganto AI">
                <header className="aic-head">
                    <span className="aic-avatar"><Icon name="bot" size="22" /></span>
                    <div className="aic-who"><b>Reganto AI</b><small>Online · ask me anything about our work</small></div>
                    <button type="button" className="aic-reset" data-aic-reset title="New chat" aria-label="New chat">↺</button>
                    <button type="button" className="aic-x" data-aic-close aria-label="Close chat">×</button>
                </header>
                <div className="aic-log" id="aicLog" aria-live="polite"></div>
                <div className="aic-chips" id="aicChips">
                    <button type="button">I want to start a project</button>
                    <button type="button">What services do you offer?</button>
                    <button type="button">Show your recent projects</button>
                    <button type="button">How can I book a service?</button>
                    <button type="button">Where is your office?</button>
                </div>
                <form className="aic-form" id="aicForm" autoComplete="off">
                    <input type="text" id="aicInput" maxLength="500" placeholder="Type a message…" aria-label="Message" />
                    <button type="submit" aria-label="Send"><Icon name="send" size="18" /></button>
                </form>
            </section>
        </div>
    );
}

export default function SiteLayout({ children }) {
    const { props, url: pageUrl } = usePage();
    const { site = {}, navServices = [], locale = 'en', year, aiChatUrl } = props;
    const path = pageUrl.split(/[?#]/)[0] || '/';
    const frame = useRef(null);

    // Bangla visitors: translate the page as React renders it (before the browser paints)
    useLayoutEffect(() => watchTranslations(document.body), []);
    useEffect(() => {
        const offLinks = initLinks();
        const offPop = initServicePop();
        return () => { offLinks(); offPop(); };
    }, []);

    return (
        <>
            <Announce site={site} />
            <div className="page" id="top" ref={frame}>
                <Navbar site={site} navServices={navServices} locale={locale} path={path} />
                <main>{children}</main>
                <Footer site={site} navServices={navServices} year={year} />
            </div>
            <Chat site={site} />
            <AiChat chatUrl={aiChatUrl} />
        </>
    );
}
