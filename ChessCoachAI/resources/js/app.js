import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

/**
 * This is the Vue + Inertia bootstrap — equivalent to Program.cs in ASP.NET.
 *
 * resolvePageComponent maps an Inertia page name like 'ChessCoach'
 * to the file resources/js/Pages/ChessCoach.vue automatically.
 */
createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
