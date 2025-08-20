import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

createInertiaApp({
	resolve: name => {
		const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
		return pages[`./Pages/${name}.vue`];
	},
	setup({ el, App, props, plugin }) {
		const app = createApp({ render: () => h(App, props) });
		app.use(plugin);

		// Add global route helper using Ziggy
		app.config.globalProperties.route = (name, params, absolute) => {
			return window.route ? window.route(name, params, absolute) : '#';
		};

		app.mount(el);
	},
});

// Inertia progress plugin omitted to avoid needing an extra dependency in this repo
