/* The home page sections below the hero: the same markup as resources/views/home/*.blade.php, so the same CSS
   and behaviours apply. */
import Icon from '../../Components/Icon';
import { pad } from './serviceArt';

const LOGO_ICONS = ['cloud', 'hex', 'leaf', 'wave', 'gear', 'ring'];

export function Logos({ clients }) {
    if (!clients.length) return null;
    const rows = [];
    for (let c = 0; c < 6; c++) {
        clients.forEach((client, i) => rows.push(
            <span className="brand-item" key={c + '-' + i} aria-hidden={c > 0 ? 'true' : undefined}>
                {client.logo ? <img className="brand-logo" src={client.logo} alt="" /> : <Icon name={LOGO_ICONS[i % 6]} />} {client.name}
            </span>,
        ));
    }
    return (
        <section className="brand-marquee hm-logos" aria-label="Trusted by teams that value quality">
            <div className="brand-track">{rows}</div>
        </section>
    );
}

/* "Our Services": pinned, scroll-driven, one step per category; the laptop docks in [data-dv-slot=services] */
export function Services({ services }) {
    const n = services.length;
    return (
        <section className="svs" data-svs style={{ '--n': n, '--pp': 0 }}>
            <div className="svs-track">
                <div className="svs-pin">
                    <div className="svs-head wk2-head">
                        <span className="svc2-eyebrow">OUR SERVICES</span>
                        <h2>Everything your business <em>needs.</em></h2>
                    </div>
                    <div className="svs-left">
                        <div className="svs-mslot" data-dv-slot="services" aria-hidden="true"></div>
                        <div className="svs-panels">
                            {services.map((s, i) => (
                                <article key={s.slug} className={'svs-panel' + (i === 0 ? ' on' : '')} data-svs-panel>
                                    <p className="svs-count"><b>{pad(i + 1)}</b> / {pad(n)}</p>
                                    <h3 className="svs-name">{s.title}</h3>
                                    <p className="svs-short">{s.short}</p>
                                    <p className="svs-out"><b>{s.items_count}+</b><span>services in this category</span></p>
                                    <ul className="svs-tools">{s.items.map((it) => <li key={it}>{it}</li>)}</ul>
                                    <a className="svs-cta" href={'/services/' + s.slug}>Explore the service <span><Icon name="arrow-right" size="14" /></span></a>
                                </article>
                            ))}
                        </div>
                        <div className="svs-index" role="tablist" aria-label="Service categories">
                            {services.map((s, i) => (
                                <button key={s.slug} type="button" className={i === 0 ? 'on' : ''} data-svs-step={i} aria-selected={i === 0 ? 'true' : 'false'}><span>{pad(i + 1)}</span> {s.title}</button>
                            ))}
                        </div>
                    </div>
                    <div className="svs-scene" aria-hidden="true">
                        <svg className="svs-logo" viewBox="0 0 100 100" fill="none" strokeWidth="7" strokeLinecap="round" strokeLinejoin="round">
                            <path className="t" pathLength="100" d="M15.2 26.5 A42 42 0 1 1 79.2 80.2" />
                            <path className="t" pathLength="100" d="M7 35.4 H64 A9 9 0 0 1 64 53.4 H39 L80 81" />
                            <path className="g" pathLength="100" d="M8.4 44.2 A42 42 0 0 0 58.7 91.1" />
                            <path className="g" pathLength="100" d="M20 52 L66 85" />
                        </svg>
                        <div className="svs-iso">
                            {services.map((s, i) => (
                                <div key={s.slug} className={'svs-slab' + (i === 0 ? ' on' : '')} style={{ '--i': i, '--z': n - 1 - i }} data-svs-slab>
                                    <div className="svs-top">
                                        <span className="svs-grid"></span>
                                        <span className="svs-tn">{pad(i + 1)}</span>
                                        <span className="svs-tt">{s.title}</span>
                                    </div>
                                    <div className="svs-sf"></div>
                                    <div className="svs-sr"></div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}

const WORK_TABS = [['All', 'All'], ['Web Development', 'Web Development'], ['Software', 'Software'], ['UI/UX Design', 'UI/UX Design'], ['Branding', 'Graphics & Branding']];

export function Work({ projects, workOrder }) {
    return (
        <section className="block wk2 hm hm-work" data-wk2 data-order={JSON.stringify(workOrder)} data-replay data-threshold="0.12">
            <div className="hm-head">
                <p className="hm-eb"><i></i>[ OUR WORK ]</p>
                <h2 className="hm-h2">Recent work that <em>drives results.</em></h2>
            </div>
            <div className="wk2-tabs" role="tablist">
                {WORK_TABS.map(([label, tag], i) => (
                    <button key={tag} type="button" role="tab" className={i === 0 ? 'on' : ''} data-tag={tag} aria-selected={i === 0 ? 'true' : 'false'}>{label}</button>
                ))}
            </div>
            <div className="wk2-grid hw-bento" id="wk2Grid" data-limit="7">
                {projects.map((p) => (
                    <a key={p.slug} href={'/projects?open=' + p.slug} className={'wk2-card' + (p.portrait ? ' is-portrait' : '')} data-slug={p.slug} data-i={p.i} data-tags={JSON.stringify(p.tags)} hidden>
                        <span className="wk2-shot"><img src={p.cover} alt={p.title} loading="lazy" draggable="false" /></span>
                        <span className="wk2-cap"><small>{p.category}</small><b>{p.title}</b></span>
                        <span className="wk2-go"><Icon name="arrow-right" size="16" /></span>
                    </a>
                ))}
            </div>
            <p className="wk2-more"><a className="btn btn-sm" href="/projects">View all projects <Icon name="arrow-circle" size="16" /></a></p>
        </section>
    );
}

const FEATURES = [
    ['chart', 'Websites that feel alive', 'High-end interfaces with thoughtful motion, strong storytelling and conversion-focused UX.'],
    ['user', 'Products people want to use', 'We turn complicated workflows into simple, beautiful and scalable digital products.'],
    ['users', 'SaaS & Platforms', 'Scalable dashboards, admin systems, reseller platforms, automation and powerful backends.'],
    ['hex', 'Brands with a point of view', 'Visual identities, digital systems and brand experiences designed to stay recognizable everywhere.'],
];
const REASONS = ['Strategy-Led UI/UX That Converts', 'Custom Builds Tailored to Your Goals', 'Transparent Communication & Process', 'Scalable Frontend & Backend Engineering', 'Performance, Speed & Quality Standards', 'Reliable Support & Long-Term Partnership'];

export function Features({ siteName }) {
    return (
        <section className="hm hm-why" data-replay data-threshold="0.2">
            <div className="hm-head">
                <p className="hm-eb"><i></i>[ WHY CHOOSE US ]</p>
                <h2 className="hm-h2">Why businesses choose <em>{siteName}.</em></h2>
                <p className="hm-sub">From first idea to final experience.</p>
            </div>
            <div className="hw-cols">
                {FEATURES.map(([icon, title, text], i) => (
                    <article key={title} style={{ '--k': i }}>
                        <span className="hw-ico"><Icon name={icon} size="22" /></span>
                        <h3>{title}</h3>
                        <p>{text}</p>
                    </article>
                ))}
            </div>
            <ul className="hw-list">
                {REASONS.map((text, i) => (
                    <li key={text} style={{ '--k': i }}>
                        <svg className="hw-tick" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5" pathLength="1" /></svg>{text}
                    </li>
                ))}
            </ul>
            <p className="hm-cta"><a className="hm-btn" href="#work">Explore our work <span><Icon name="arrow-right" size="14" /></span></a></p>
        </section>
    );
}

export function Reviews({ testimonials, reviewUrl }) {
    return (
        <section className="block hm hm-rev" data-replay data-threshold="0.2">
            <div className="hm-head">
                <p className="hm-eb"><i></i>[ CLIENT REVIEWS ]</p>
                <h2 className="hm-h2">What clients say about <em>working with us.</em></h2>
                <p className="hm-sub"><b>★ 4.9</b> out of 5 from 30+ reviews. <a href="https://clutch.co/">Read them on Clutch</a></p>
            </div>
            {testimonials.length ? (
                <div className="cf" data-carousel role="region" aria-roledescription="carousel" aria-label="Client reviews" tabIndex="0">
                    <button type="button" className="cf-arrow prev" data-prev aria-label="Previous review"><Icon name="chevron" size="18" style={{ transform: 'rotate(180deg)' }} /></button>
                    <div className="cf-stage">
                        {testimonials.map((t, i) => (
                            <figure key={i} className="cf-card rv-card" data-card>
                                <span className="rv-mark" aria-hidden="true">“</span>
                                <div className="rv-top">
                                    <div className="cf-stars rv-stars" aria-label={t.rating + ' out of 5 stars'}><span>{'★'.repeat(t.rating)}</span>{'★'.repeat(5 - t.rating)}</div>
                                    <span className="rv-score">{t.rating}.0</span>
                                </div>
                                <blockquote>{t.quote}</blockquote>
                                <figcaption>
                                    {t.photo
                                        ? <img className="cf-av rv-photo" src={t.photo} alt="" width="44" height="44" loading="lazy" decoding="async" />
                                        : <i className="cf-av">{(t.name || '').trim().charAt(0).toUpperCase()}</i>}
                                    <span><b>{t.name}</b>{t.role ? <small>{t.role}</small> : null}</span>
                                </figcaption>
                            </figure>
                        ))}
                    </div>
                    <button type="button" className="cf-arrow next" data-next aria-label="Next review"><Icon name="chevron" size="18" /></button>
                    <div className="cf-dots">
                        {testimonials.map((t, i) => <button key={i} type="button" data-dot={i} aria-label={'Show review ' + (i + 1)}></button>)}
                    </div>
                </div>
            ) : null}
            <div className="rv-cta">
                <p>Worked with us? <span>Tell others how it went.</span></p>
                <button type="button" className="hm-btn" data-review-open>Write a review <span><Icon name="star" size="16" /></span></button>
            </div>
            <dialog className="rv-dlg" data-review-dialog aria-labelledby="rv-title">
                <form className="rv-form" method="POST" action={reviewUrl} encType="multipart/form-data" data-review-form noValidate>
                    <button type="button" className="rv-x" data-review-close aria-label="Close">×</button>
                    <p className="hm-eb"><i></i>[ YOUR REVIEW ]</p>
                    <h3 id="rv-title">Share your experience</h3>
                    <p className="rv-note">Your review shows on the site after our team checks it.</p>
                    <fieldset className="rv-rate">
                        <legend>Your rating *</legend>
                        <div className="rv-rate-stars">
                            {[5, 4, 3, 2, 1].map((n) => [
                                <input key={'i' + n} type="radio" id={'rv-r' + n} name="rating" value={n} defaultChecked={n === 5} />,
                                <label key={'l' + n} htmlFor={'rv-r' + n} title={n + ' out of 5 stars'}>★</label>,
                            ])}
                        </div>
                        <em data-err="rating"></em>
                    </fieldset>
                    <div className="rv-row">
                        <label>Your name *<input type="text" name="name" maxLength="120" autoComplete="name" required /><em data-err="name"></em></label>
                        <label>Role / company<input type="text" name="role" maxLength="150" placeholder="e.g. Owner, ABC Fashion" autoComplete="organization-title" /><em data-err="role"></em></label>
                    </div>
                    <label>Your review *<textarea name="quote" rows="4" minLength="10" maxLength="600" placeholder="How was working with us?" required></textarea><small className="rv-count"><b data-review-count>0</b>/600</small><em data-err="quote"></em></label>
                    <label className="rv-file">Your photo (optional)
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" />
                        <small>JPG, PNG or WebP, up to 2 MB.</small>
                        <em data-err="photo"></em>
                    </label>
                    <input className="rv-hp" type="text" name="website" tabIndex="-1" autoComplete="off" aria-hidden="true" hidden />
                    <p className="rv-status" data-review-status role="status" aria-live="polite"></p>
                    <button type="submit" className="hm-btn rv-send">Send review <span><Icon name="send" size="16" /></span></button>
                </form>
            </dialog>
        </section>
    );
}

export function Faq({ faqs }) {
    if (!faqs.length) return null;
    return (
        <section className="block faq hm hm-faq" data-replay data-threshold="0.15">
            <div className="hf-side">
                <div className="hm-head">
                    <p className="hm-eb"><i></i>[ FAQ ]</p>
                    <h2 className="hm-h2">Questions, <em>answered.</em></h2>
                    <p className="hm-sub">Can't find what you need? Ask us directly.</p>
                </div>
                <a className="hm-btn" href="/contact">Ask a question <span><Icon name="arrow-right" size="14" /></span></a>
            </div>
            <div className="hf-list">
                {faqs.map((f, i) => (
                    <details key={i} style={{ '--k': i }}>
                        <summary>
                            <span className="faq-q">{f.question}</span>
                            <span className="hf-plus" aria-hidden="true"></span>
                        </summary>
                        <p>{f.answer}</p>
                    </details>
                ))}
            </div>
        </section>
    );
}

export function Dock({ site }) {
    return (
        <section className="h2-dock" data-h2-dock>
            <div className="h2-dock-copy">
                <p className="h2-eb"><i></i>YOUR TURN</p>
                <h2>Your project could be <em>on this screen next.</em></h2>
                <p>Tell us what you want to build. We read every brief and reply within 30 minutes.</p>
                <div className="h2-cta">
                    <a className="hm-btn" href="/contact#brief">Start a project <span><Icon name="arrow-right" size="14" /></span></a>
                    {site.whatsapp ? <a className="h2-ghost" href={site.whatsapp} target="_blank" rel="noreferrer">Chat on WhatsApp</a> : null}
                </div>
            </div>
            <div className="h2-dslot" data-dv-slot="dock" aria-hidden="true"></div>
        </section>
    );
}
