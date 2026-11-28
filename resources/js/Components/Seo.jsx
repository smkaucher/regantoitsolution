/* The page's SEO tags after an Inertia page change. The first page load gets the same tags from the server
   (resources/views/inertia.blade.php); every tag here has the same head-key as its server twin, so Inertia
   replaces them instead of adding duplicates. */
import { Head, usePage } from '@inertiajs/react';
import { tr } from '../lib/translate';

const clip = (s, n) => {
    const text = (s || '').replace(/<[^>]*>/g, '');
    return text.length > n ? text.slice(0, n) + '...' : text;
};

export default function Seo() {
    const { props } = usePage();
    const meta = props.meta || {};
    const siteName = props.site?.site_name || 'Reganto IT Solution';
    const title = tr(meta.title || siteName + ' – One studio. Every digital possibility.');
    const desc = tr(meta.description || props.site?.tagline || '');
    const image = meta.image || window.location.origin + '/assets/img/logo.png';
    const href = window.location.origin + window.location.pathname;
    return (
        <Head>
            <title>{title}</title>
            <meta head-key="description" name="description" content={clip(desc, 160)} />
            {meta.robots ? <meta head-key="robots" name="robots" content={meta.robots} /> : null}
            <link head-key="canonical" rel="canonical" href={href} />
            <meta head-key="og:site_name" property="og:site_name" content={siteName} />
            <meta head-key="og:type" property="og:type" content={meta.type || 'website'} />
            <meta head-key="og:title" property="og:title" content={title} />
            <meta head-key="og:description" property="og:description" content={clip(desc, 200)} />
            <meta head-key="og:url" property="og:url" content={href} />
            <meta head-key="og:image" property="og:image" content={image} />
            <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
            <meta head-key="twitter:title" name="twitter:title" content={title} />
            <meta head-key="twitter:description" name="twitter:description" content={clip(desc, 200)} />
            <meta head-key="twitter:image" name="twitter:image" content={image} />
            {meta.jsonLd ? <script head-key="json-ld" type="application/ld+json">{JSON.stringify(meta.jsonLd)}</script> : null}
        </Head>
    );
}
