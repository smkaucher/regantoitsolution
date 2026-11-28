import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

// Builds the React public pages into public/build (npm run build). The server only needs that folder:
// no Node, npm or terminal on the hosting.
//
// The pages are written in React, but run on Preact (preact/compat): the same components and hooks, about 60 KB
// less JavaScript for every visitor. To run them on React itself instead, set USE_PREACT to false (react and
// react-dom are installed too). Nothing else changes.
const USE_PREACT = true;

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
    ],
    resolve: USE_PREACT ? {
        alias: {
            'react-dom/client': 'preact/compat/client',
            'react-dom': 'preact/compat',
            'react/jsx-runtime': 'preact/jsx-runtime',
            'react/jsx-dev-runtime': 'preact/jsx-dev-runtime',
            react: 'preact/compat',
        },
    } : {},
});
