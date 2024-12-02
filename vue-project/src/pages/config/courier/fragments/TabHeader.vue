<template>
    <div v-if="courierCompanyNames?.length" class="flex justify-center">
        <button 
            v-for="(item: string, index: number) in courierCompanyNames"
            :key="index"
            class="capitalize px-5 py-2"
            :class="[
                item.slug == activeTab ? 'bg-white rounded-t-lg text-orange-500' : 'bg-gray-100 text-gray-400',
                hasUnsavedData ? 'opacity-40' : ''
            ]"
            @click="changeTab(item.slug)"
        >
            {{ item.title }}
        </button>
    </div>
</template>

<script setup lang="ts">
    import { inject } from 'vue'

    const {
        courierCompanyNames,
        changeTab,
        hasUnsavedData,
        activeTab
    } = inject("useCourier")
</script>