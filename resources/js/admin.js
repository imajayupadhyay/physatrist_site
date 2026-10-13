import './bootstrap';
import '../css/admin.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Admin panel entry (rendered by resources/views/admin/app.blade.php).
createInertiaApp({
    title: (title) => (title ? `${title} · Admin` : `Admin · ${appName}`),
    resolve: (name) =>
        resolvePageComponent(
            `./Admin/Pages/${name}.vue`,
            import.meta.glob(['./Admin/Pages/**/*.vue', '!./Admin/Pages/**/Components/**']),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#B8925A',
    },
});
