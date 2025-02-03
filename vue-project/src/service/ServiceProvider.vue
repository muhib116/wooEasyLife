<template>
    <slot></slot>
</template>

<script setup lang="ts">
    import { provide } from 'vue'
    import {
        useServiceProvider
    } from './useServiceProvider'

    const _useServiceProvider = useServiceProvider()
    provide('useServiceProvider', _useServiceProvider)
</script>