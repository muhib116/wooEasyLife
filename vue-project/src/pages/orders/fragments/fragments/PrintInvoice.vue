<template>
    <Invoice.Simple />
</template>

<script setup lang="ts">
    import { inject } from 'vue'
    import { Invoice } from '@/components'

    const { showInvoices } = inject('useOrders')
</script>