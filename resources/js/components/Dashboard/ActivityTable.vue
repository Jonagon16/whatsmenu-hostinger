<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full">
        <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4 flex items-center">
                <span class="mr-2">📋</span> Actividad Reciente
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">De</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mensaje</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="log in activities" :key="log.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ new Date(log.created_at).toLocaleString() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ log.from_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-xs" :title="log.message">{{ log.message }}</td>
                        </tr>
                        <tr v-if="activities.length === 0">
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay actividad reciente.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    activities: Array
});
</script>
