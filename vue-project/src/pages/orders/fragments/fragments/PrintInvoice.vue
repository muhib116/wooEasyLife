<template>
    <Invoice.Simple
        v-for="(order, index) in selectedOrders"
        :key="index"
        :order="order"
    />
</template>

<script setup lang="ts">
    import { inject } from 'vue'
    import { Invoice } from '@/components'

    const {
        showInvoices,
        selectedOrders
    } = inject('useOrders')
</script>