<template>
    <div>
        <h3 class="text-xl font-semibold text-gray-900">
            {{ title }}
        </h3>
        <p v-if="subtitle" class="text-lg text-gray-600">
            {{ subtitle }}
        </p>
    </div>
</template>

<script setup lang="ts">
    defineProps<{
        title: string,
        subtitle?: string
    }>()
</script>