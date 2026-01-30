import './bootstrap';

import Alpine from 'alpinejs';
import { createApp } from 'vue';
import DashboardApp from './components/Dashboard/DashboardApp.vue';

window.Alpine = Alpine;
Alpine.start();

const app = createApp({});
app.component('dashboard-app', DashboardApp);

// Debugging
app.config.errorHandler = (err, instance, info) => {
    console.error("Vue Error:", err);
    console.info("Info:", info);
    alert('Global Vue Error: ' + err.message);
};

if (document.getElementById('vue-app')) {
    console.log('Mounting Vue App...');
    try {
        app.mount('#vue-app');
        console.log('Vue App Mounted Successfully');
    } catch (e) {
        console.error('Mount Error:', e);
        alert('Mount Error: ' + e.message);
    }
}
