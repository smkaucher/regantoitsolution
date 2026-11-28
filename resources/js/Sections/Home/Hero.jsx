/* Hero: copy on the left; the laptop opens on the right and five service screens float around it
   (markup of home2.blade.php; the laptop itself is Laptop.jsx, placed into [data-dv-slot=hero]). */
import Icon from '../../Components/Icon';
import { Art, ICONS, kindOf } from './serviceArt';

const STATS = [[80, '+', 0, 'Projects Delivered'], [40, '+', 0, 'Happy Clients'], [4.9, '★', 1, 'Client Rating'], [24, '/7', 0, 'Digital Support']];

export default function Hero({ site, year, heroServices }) {
    return (
        <section className="h2-hero" data-h2-hero>
            <div className="h2-bg" aria-hidden="true"><i className="h2-sweep"></i></div>

            <div className="h2-copy">
                <p className="h2-eb"><i></i>{'DIGITAL STUDIO · ' + year}</p>
                <h1 className="h2-title">
                    <span><b>One studio.</b></span>
                    <span><b>Every <em>digital</em></b></span>
                    <span><b>possibility.</b></span>
                </h1>
                <p className="h2-sub">{site.hero_subtitle || 'Software, UI/UX design, branding, marketing and cyber security for startups and growing companies.'}</p>
                <div className="h2-cta">
                    <a className="hm-btn" href="/contact#brief">Start a project <span><Icon name="arrow-right" size="14" /></span></a>
                    <a className="h2-ghost" href="#h2-services">See what we do</a>
                </div>
                <div className="h2-stats" data-replay>
                    {STATS.map(([value, suffix, decimals, label]) => (
                        <div key={label}><strong><span data-count={value} data-suffix={suffix} data-decimals={decimals}>0</span></strong><small>{label}</small></div>
                    ))}
                </div>
            </div>

            <div className="h2-stage" data-h2-stage>
                {/* Coverflow: five glass screens, two on each side of the laptop; laptopTravel.js sets their places */}
                <div className="cfv" aria-label="Our services">
                    {heroServices.map((s, i) => {
                        const k = kindOf(s.slug);
                        return (
                            <a key={s.slug} className="cv gbox" data-cv href={'/services/' + s.slug} style={{ '--d': i }}>
                                <span className="sl on">
                                    <span className="sl-txt">
                                        <svg className="gp-ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" dangerouslySetInnerHTML={{ __html: ICONS[k] }} />
                                        <strong>{s.title}</strong>
                                        {s.short ? <small>{s.short}</small> : null}
                                        <span className="gp-btn">Learn More <Icon name="arrow-right" size="10" /></span>
                                    </span>
                                    <span className="sl-art">
                                        <span className="gp-art" data-art={k} aria-hidden="true"><Art kind={k} /></span>
                                    </span>
                                </span>
                            </a>
                        );
                    })}
                </div>
                <div className="h2-lslot" data-dv-slot="hero" aria-hidden="true"></div>
            </div>
        </section>
    );
}
