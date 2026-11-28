/* The React public pages (Inertia). Each page in Pages/ is rendered inside the shared SiteLayout. */
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import SiteLayout from './Layouts/SiteLayout';
import './lib/translate';

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx');
        return pages[`./Pages/${name}.jsx`]().then((module) => {
            const page = module.default;
            page.layout = page.layout || ((p) => <SiteLayout>{p}</SiteLayout>);
            return page;
        });
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: { color: '#0d9488', showSpinner: false },
});
