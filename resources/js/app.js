import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { syncSite } from './Config/site';
import reveal from './Directives/reveal';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    // Pages set their full title via <Head>; fall back to the app name.
    title: (title) => title || appName,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            // Page-scoped components live under Pages/<Page>/Components and are not pages themselves.
            import.meta.glob(['./Pages/**/*.vue', '!./Pages/**/Components/**']),
        ),
    setup({ el, App, props, plugin }) {
        // Keep Config/site.js in sync with the admin-managed contact details.
        syncSite(props.initialPage.props.contact);
        router.on('navigate', (event) => syncSite(event.detail.page.props.contact));

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .directive('reveal', reveal)
            .mount(el);
    },
    progress: {
        color: '#B8925A',
    },
});
