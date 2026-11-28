/* Home page (the /home2 design): hero with the laptop and five service screens, client logos, the pinned Services
   section where the laptop becomes a monitor, recent work, why us, reviews, FAQ, and the dock where the laptop lands.
   "Recent work", reviews and FAQ arrive right after the page shows (deferred props); until then each keeps a
   placeholder of about its height, as on the Blade page. */
import { useLayoutEffect, useRef } from 'react';
import { Deferred, usePage } from '@inertiajs/react';
import Seo from '../Components/Seo';
import Hero from '../Sections/Home/Hero';
import Laptop, { useLaptopElement } from '../Sections/Home/Laptop';
import { Dock, Faq, Features, Logos, Reviews, Services, Work } from '../Sections/Home/Sections';
import useBehaviors from '../lib/useBehaviors';
import { initAnimations, initCarousels, initReviewForms, initServiceScroll, initWorkFilter } from '../lib/behaviors';
import { startLaptop } from '../lib/laptopTravel';

const Placeholder = ({ h }) => <div className="lazy-ph is-loading" style={{ '--h': h + 'rem' }} aria-busy="true"></div>;

/* One section below the fold; its behaviours start once its data is there. */
function Part({ id, children, inits }) {
    const ref = useRef(null);
    useBehaviors(ref, inits, []);
    return <div id={id} data-h2-part={id} ref={ref}>{children}</div>;
}

export default function Home({ clients, services, projects, workOrder, testimonials, faqs }) {
    const { props } = usePage();
    const { site = {}, year } = props;
    const top = useRef(null), home = useRef(null);
    const dv = useLaptopElement();
    const heroServices = services.slice(0, 5);

    useBehaviors(top, [initAnimations, initServiceScroll], []);

    // The laptop: placed into the page, then moved by the scroll (see laptopTravel.js)
    useLayoutEffect(() => {
        home.current.appendChild(dv);
        return startLaptop({ dv, home: home.current, page: top.current });
    }, [dv]);

    return (
        <>
            <Seo />
            <div className="h2" data-h2 ref={top}>
                <Hero site={site} year={year} heroServices={heroServices} />
                <Logos clients={clients} />
                <div id="h2-services"><Services services={services} /></div>

                <Deferred data={['projects', 'workOrder']} fallback={<div id="work" data-h2-part="work"><Placeholder h={36} /></div>}>
                    <Part id="work" inits={[initAnimations, initWorkFilter]}><Work projects={projects || []} workOrder={workOrder || {}} /></Part>
                </Deferred>
                <Part id="features" inits={[] /* in the page from the start: covered by the page's own initAnimations */}><Features siteName={site.site_name || 'Reganto IT Solution'} /></Part>
                <Deferred data="testimonials" fallback={<div id="reviews" data-h2-part="reviews"><Placeholder h={28} /></div>}>
                    <Part id="reviews" inits={[initAnimations, initCarousels, initReviewForms]}><Reviews testimonials={testimonials || []} reviewUrl="/reviews" /></Part>
                </Deferred>
                <Deferred data="faqs" fallback={<div id="faq" data-h2-part="faq"><Placeholder h={26} /></div>}>
                    <Part id="faq" inits={[initAnimations]}><Faq faqs={faqs || []} /></Part>
                </Deferred>

                <Dock site={site} />
            </div>
            {/* #dv lives here while it is drawn over the window (display: contents adds no box of its own) */}
            <div ref={home} style={{ display: 'contents' }}></div>
            <Laptop el={dv} heroServices={heroServices} services={services} />
        </>
    );
}
