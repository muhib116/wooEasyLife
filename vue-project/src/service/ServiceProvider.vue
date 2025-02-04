<template>
    <slot></slot>
</template>

<script setup lang="ts">
    import { onMounted, provide } from 'vue'
    import { isEmpty } from 'lodash'
    import {
        useServiceProvider
    } from './useServiceProvider'

    const _useServiceProvider = useServiceProvider()
    const { loadUserData, userData } = _useServiceProvider

    onMounted(() => {
        if(isEmpty(userData.value)) {
            loadUserData()
        }
    })

    provide('useServiceProvider', _useServiceProvider)
</script>