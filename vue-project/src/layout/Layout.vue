<template>
    <div class="bg-gray-200 min-h-screen">
        <Navigation class="sticky top-8 z-50"/>
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
    import { Navigation } from '@components'
</script>