/* The travelling laptop (#dv) of the home page, markup from home2.blade.php.
   laptopTravel.js moves #dv between the slots on the page (hero, Services, dock) and the 3D module moves it into
   its own layer, so #dv must not be a node React places itself: React renders the laptop's inside through a
   portal into a plain <div id="dv"> made here, and laptopTravel.js decides where that div lives. */
import { useState } from 'react';
import { createPortal } from 'react-dom';
import Icon from '../../Components/Icon';
import { ICONS, kindOf, pad } from './serviceArt';

export function useLaptopElement() {
    const [el] = useState(() => {
        const d = document.createElement('div');
        d.className = 'dv';
        d.id = 'dv';
        d.setAttribute('aria-hidden', 'true');
        return d;
    });
    return el;
}

function Inside({ heroServices, services }) {
    return (
        <div className="dv-3d">
            <div className="dv-lid">
                <div className="dv-bezel">
                    <i className="dv-cam"></i>
                    <div className="dv-screen">
                        {/* 1. Hero: the current service of the carousel */}
                        <div className="dv-scr" data-scr="site" data-sl-track="0">
                            {heroServices.map((s, i) => (
                                <article className="ls" data-sl-card key={s.slug}>
                                    <div className="ls-txt">
                                        <span className="ls-num">{pad(i + 1)} / {pad(heroServices.length)}</span>
                                        <h3>{s.title}</h3>
                                        {s.short ? <p>{s.short}</p> : null}
                                        <ul>{s.items.slice(0, 3).map((it) => <li key={it}>{it}</li>)}</ul>
                                        <span className="ls-go">View service <Icon name="arrow-right" size="12" /></span>
                                    </div>
                                    <div className="ls-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" dangerouslySetInnerHTML={{ __html: ICONS[kindOf(s.slug)] }} /></div>
                                </article>
                            ))}
                            <div className="ls-dots">{heroServices.map((s) => <i data-sl-dot key={s.slug}></i>)}</div>
                        </div>
                        {/* 2. Services: one panel per category, switched in step with the pinned section */}
                        <div className="dv-scr" data-scr="svc">
                            <div className="sv-bar"><i></i><i></i><i></i><span>Our services</span><b data-sv-count>01 / {pad(services.length)}</b></div>
                            {services.map((s, i) => (
                                <article className={'sv-panel' + (i === 0 ? ' on' : '')} data-dv-panel key={s.slug}>
                                    <span className="sv-num">{pad(i + 1)}</span>
                                    <h3>{s.title}</h3>
                                    {s.short ? <p>{s.short}</p> : null}
                                    <ul>{s.items.map((it) => <li key={it}>{it}</li>)}</ul>
                                    <span className="sv-foot"><b>{s.items_count}+</b> services in this category</span>
                                </article>
                            ))}
                            <div className="sv-progress"><i data-sv-progress></i></div>
                        </div>
                        {/* 3. On the way down: the section being read */}
                        <div className="dv-scr" data-scr="mini">
                            <span className="mi-k">You're viewing</span>
                            <strong data-mini-label>Our work</strong>
                            <span className="mi-bar"><i data-mini-progress></i></span>
                        </div>
                        {/* 4. Landed: start a project */}
                        <div className="dv-scr" data-scr="cta">
                            <img src="/assets/img/logo.png" alt="" width="300" height="100" />
                            <strong>Let's build yours.</strong>
                            <span className="ct-line"></span><span className="ct-line short"></span><span className="ct-line"></span>
                            <b className="ct-btn">Start a project</b>
                        </div>
                    </div>
                </div>
            </div>
            <div className="dv-base"><div className="dv-deck"><span className="dv-keys"></span><span className="dv-pad"></span></div><div className="dv-lip"><i></i></div></div>
        </div>
    );
}

export default function Laptop({ el, heroServices, services }) {
    return createPortal(<Inside heroServices={heroServices} services={services} />, el);
}
