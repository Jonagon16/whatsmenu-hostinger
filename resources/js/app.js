import './bootstrap';

import Alpine from 'alpinejs';
import { createApp } from 'vue';
import DashboardApp from './components/Dashboard/DashboardApp.vue';

window.Alpine = Alpine;
Alpine.start();

const app = createApp({});
app.component('dashboard-app', DashboardApp);

if (document.getElementById('vue-app')) {
    app.mount('#vue-app');
}
