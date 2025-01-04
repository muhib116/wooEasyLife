<template>

    <div class="flex gap-4 justify-center print:hidden">
        <Button.Primary
            @click="showInvoices = false"
        >
            <Icon
                name="PhCaretLeft"
            />
            Back to List
        </Button.Primary>
        <Button.Primary
            @click="handlePrint"
        >
            <Icon
                name="PhPrinter"
            />
            Print Now
        </Button.Primary>
    </div>
    <Invoice.Simple
        v-for="(order, index) in selectedOrders"
        :key="index"
        :order="order"
    />
</template>

<script setup lang="ts">
    import { inject } from 'vue'
    import { Invoice, Icon, Button } from '@/components'

    const {
        showInvoices,
        selectedOrders
    } = inject('useOrders')

    const handlePrint = () => {
        window.print()
    }
</script>