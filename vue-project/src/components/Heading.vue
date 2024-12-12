<template>
    <div>
        <h3 class="text-xl font-semibold text-gray-900">
            {{ title }}
        </h3>
        <div
            v-if="subtitle" class="text-lg text-gray-500"
            v-html="subtitle"
        ></div>
    </div>
</template>

<script setup lang="ts">
    defineProps<{
        title: string,
        subtitle?: string
    }>()
</script>