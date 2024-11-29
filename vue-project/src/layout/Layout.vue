<template>
    <div class="bg-gray-100 min-h-screen pb-10">
        <Navigation 
            class="sticky z-50"
            :class="isDevelopmentMode ? 'top-0' : 'top-8'"
        />
        <main class="mt-6">
            <slot></slot>
        </main>
    </div>
</template>

<script setup lang="ts">
    import { Navigation } from '@components'

    const isDevelopmentMode =  import.meta.env.DEV
</script>