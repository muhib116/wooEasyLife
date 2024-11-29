<template>
    <div class="relative">
        <Heading
            title="Recent Orders"
            class="mb-4 px-6"
        />
        <Loader
            class="absolute left-1/2 -translate-x-1/2 z-30"
            :active="isLoading"
        />
        <Table.Table v-if="orders.length">
            <TableHeader />
            <Table.TBody>
                <TableRow 
                    v-for="(order, index) in orders"
                    :key="index"
                    :order="order"
                />
            </Table.TBody>
        </Table.Table>
    </div>
</template>

<script setup lang="ts">
    import { Table, Loader, Heading, Icon } from '@components'
    import { inject } from 'vue'
    import TableHeader from './fragments/TableHeader.vue'
    import TableRow from './fragments/TableRow.vue'

    const {
        orders,
        isLoading
    } = inject('useOrders')
</script>