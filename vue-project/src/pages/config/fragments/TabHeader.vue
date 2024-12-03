<template>
    <div v-if="data?.length" class="flex justify-center">
        <button 
            v-for="(item: string, index: number) in data"
            :key="index"
            class="capitalize px-5 py-2"
            :class="[
                item.slug == activeTab ? 'bg-white rounded-t-lg text-orange-500' : 'bg-gray-100 text-gray-400',
                hasUnsavedData ? 'opacity-40' : ''
            ]"
            @click="$emit('onTabChange', item.slug)"
        >
            {{ item.title }}
        </button>
    </div>
</template>

<script setup lang="ts">
    withDefaults(
        defineProps<{
            data: {title: string, slug: string}[],
            hasUnsavedData: boolean
            activeTab: string
        }>(),
        {
            hasUnsavedData: false
        }
    )
</script>