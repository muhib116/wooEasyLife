<template>
    <div
        v-if="configData"
        class="print:bg-transparent bg-gray-100 min-h-screen print:pb-0 pb-10 text-gray-600"
    >
        <Navigation 
            class="sticky z-50 print:hidden"
            :class="isDevelopmentMode ? 'top-0' : 'top-8'"
        />
        <main class="print:mt-0 mt-6">
            <slot></slot>
        </main>
    </div>
    <div v-else class="h-[100vh] relative">
        <Loader
            active
            class="absolute inset-1/2 -translate-x-1/2 -translate-y-1/2 "
            size="30"
        />
    </div>
</template>

<script setup lang="ts">
    import { Navigation, Loader } from '@components'
    import { onBeforeMount, provide, ref } from 'vue'
    import { getWPOption } from '@/api'

    const isDevelopmentMode =  import.meta.env.DEV
    const configData = ref()

    provide('configData', {configData})

    onBeforeMount(async () => {
        const { data } = await getWPOption({ option_name: 'config' })
        configData.value = data
    })
</script>