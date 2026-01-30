import './bootstrap';
import Alpine from 'alpinejs';
import { createApp, h } from 'vue';
import DashboardApp from './components/Dashboard/DashboardApp.vue';

window.Alpine = Alpine;
Alpine.start();

// TEST MODE: Simple Component to verify mounting
const TestComponent = {
    render() {
        return h('div', { class: 'p-10 bg-red-500 text-white font-bold text-2xl' }, [
            'SI VES ESTO, VUE FUNCIONA.',
            h('br'),
            'El problema estaba en DashboardApp.vue'
        ]);
    }
}

const app = createApp({});
// Temporarily use TestComponent instead of DashboardApp
app.component('dashboard-app', TestComponent);
// app.component('dashboard-app', DashboardApp); // Commented out real app

// Debugging
app.config.errorHandler = (err, instance, info) => {
    console.error("Vue Error:", err);
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
