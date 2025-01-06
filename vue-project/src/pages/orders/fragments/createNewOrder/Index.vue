<template>
    <div class="space-y-4 relative">
        <Loader
            class="absolute left-1/2 top-14 -translate-x-1/2"
            :active="isLoading"
        />
        <CustomerInfo />
        <ShippingInfo />
        <AddProduct />
    </div>
</template>

<script setup lang="ts">
    import CustomerInfo from './CustomerInfo.vue'
    import ShippingInfo from './ShippingInfo.vue'
    import AddProduct from './AddProduct.vue'
    import { useCustomOrder } from './useCustomOrder'
    import { provide } from 'vue'
    import { Loader } from '@components'

    const _useCustomOrder = useCustomOrder()
    const {
        isLoading
    } = _useCustomOrder

    provide('useCustomOrder', _useCustomOrder)
</script>