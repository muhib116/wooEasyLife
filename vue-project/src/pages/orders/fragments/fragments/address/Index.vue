<template>
    <div class="grid grid-cols-2 gap-10">
        <AddressEditor
            :address="order.billing_address"
            title="Billing Info"
        />
        <AddressEditor
            title="Shipping Info"
            :address="order.shipping_address"
            :billingAddress="order.billing_address"
        />
    </div>
</template>

<script setup lang="ts">
    import { provide } from 'vue'
    import AddressEditor from './AddressEditor.vue'
    import { useAddress } from './useAddress'

    defineProps<{
        order: object
    }>()

    const _useAddress = useAddress()
    provide('useAddress', _useAddress)
</script>