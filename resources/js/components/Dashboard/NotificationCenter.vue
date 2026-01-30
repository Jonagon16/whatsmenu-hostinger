<template>
    <div class="fixed bottom-4 right-4 z-50">
        <transition-group name="fade" tag="div">
            <div v-for="notification in notifications" :key="notification.id" class="bg-gray-900 text-white px-6 py-4 rounded-xl shadow-2xl mb-3 flex items-start min-w-[320px] border border-gray-700 backdrop-blur-sm bg-opacity-90">
                <span class="mr-3 text-xl">🔔</span>
                <div class="flex-1">
                    <div class="font-bold text-sm mb-1">{{ notification.title }}</div>
                    <div class="text-xs text-gray-300">{{ notification.message }}</div>
                </div>
                <button @click="remove(notification.id)" class="ml-3 text-gray-500 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </transition-group>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const notifications = ref([]);

const remove = (id) => {
    notifications.value = notifications.value.filter(n => n.id !== id);
};

const addNotification = (title, message) => {
    const id = Date.now() + Math.random();
    notifications.value.push({ id, title, message });
    setTimeout(() => remove(id), 6000);
};

onMounted(() => {
    // Expose for testing/other components
    window.notify = addNotification;

    // Echo listener setup would go here
    if (window.Echo) {
         window.Echo.channel('dashboard')
            .listen('DashboardNotification', (e) => {
                addNotification(e.title, e.message);
            });
    }
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: all 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
