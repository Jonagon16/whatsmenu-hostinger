<template>
    <div>
        <NotificationCenter />
        <div v-if="loading" class="flex justify-center items-center py-20">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500"></div>
        </div>
        <div v-else>
            <DashboardStats :stats="stats" />
            
            <div class="mb-8">
                <ChatDashboard />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-full">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center">
                            <span class="mr-2">📈</span> Interacciones (Últimos 7 días)
                        </h3>
                        <div class="relative h-64 w-full">
                            <canvas id="chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import Chart from 'chart.js/auto';
import DashboardStats from './DashboardStats.vue';
import ActivityTable from './ActivityTable.vue';
import NotificationCenter from './NotificationCenter.vue';
import ChatDashboard from './ChatDashboard.vue';

const stats = ref({});
const activities = ref([]);
const loading = ref(true);
let chartInstance = null;

onMounted(async () => {
    try {
        const [statsRes, activityRes, chartRes] = await Promise.all([
            axios.get('/api/dashboard/stats'),
            axios.get('/api/dashboard/activity'),
            axios.get('/api/dashboard/chart-data')
        ]);

        stats.value = statsRes.data;
        activities.value = activityRes.data;
        
        loading.value = false;
        
        await nextTick();
        initChart(chartRes.data);
    } catch (error) {
        console.error('Error loading dashboard data', error);
        loading.value = false;
    }
});

const initChart = (data) => {
    const ctx = document.getElementById('chart');
    if (!ctx) return;
    
    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Mensajes',
                data: data.map(d => d.count),
                borderColor: '#10B981', // Emerald 500
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}
</script>
