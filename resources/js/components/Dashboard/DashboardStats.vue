<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div v-for="(stat, key) in stats" :key="key" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
            <div class="text-gray-500 text-sm font-medium uppercase tracking-wider">{{ formatKey(key) }}</div>
            <div class="mt-2 text-3xl font-bold text-gray-900">{{ stat }}</div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    stats: Object
});

const formatKey = (key) => {
    return key.replace(/_/g, ' ');
}
</script>
