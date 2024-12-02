<template>
    <div class="relative min-h-[300px]">
        <Loader
            class="absolute inset-1/2 -translate-x-1/2 -translate-y-1/2"
            :active="isLoading"
        />
        <TabHeader />
        <Card.Native class="bg-white h-full w-full p-4 rounded-t-none">
            <Form />
        </Card.Native>
    </div>
</template>

<script setup lang="ts">
    import { Card, Loader, Input, Button } from '@components'
    import { useCourier } from './useCourier'
    import { provide } from 'vue'
    import TabHeader from './fragments/TabHeader.vue'
    import Form from './fragments/Form.vue'

    const _useCourier = useCourier()
    const { isLoading } = _useCourier

    provide('useCourier', _useCourier)
</script>