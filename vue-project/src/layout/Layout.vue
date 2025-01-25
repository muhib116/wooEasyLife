<template>
    <MessageBox
        v-if="hasNewOrder"
        title="New Order Received 🎉"
        type="info"
        class="fixed z-[999999] inset-x-0"
    />

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
    import { Navigation, Loader, MessageBox } from '@components'
    import { onBeforeMount, provide } from 'vue'
    import { useCourier } from '@/pages/config/courier/useCourier'
    import { useNotification } from './useNotification'
    import { useLayout } from './useLayout'
    import { useLicense } from '@/pages/config/license/UseLicense'

    const isDevelopmentMode =  import.meta.env.DEV
    const _useCourierConfig = useCourier()
    const { loadCourierConfigData } = _useCourierConfig

    const { isValidLicenseKey } = useLicense(false)
    const _useLayout = useLayout()
    const {
        configData,
        loadConfig
    } = _useLayout

    onBeforeMount(async () => {
        await loadConfig()
        if(isValidLicenseKey.value) {
            await loadCourierConfigData()
        }
    })
    
    const _useNotification = useNotification()
    const {
        hasNewOrder
    } = _useNotification

    provide('useCourierConfig', _useCourierConfig)
    provide('configData', {configData})
    provide('useNotification', _useNotification)
</script>