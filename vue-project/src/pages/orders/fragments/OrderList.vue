<template>
    <div class="relative">
        <Loader
            class="absolute left-1/2 -translate-x-1/2 z-30"
            :active="isLoading"
        />

        <OrderDetails v-if="activeOrder" />
        <div v-else>
            <Heading
                title="Recent Orders"
                class="mb-4 px-6"
            />
            <TableFilter />
            <TableHeaderAction />
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
    </div>
</template>

<script setup lang="ts">
    import { Table, Loader, Heading } from '@components'
    import { inject } from 'vue'
    import TableHeaderAction from './fragments/TableHeaderAction.vue'
    import TableHeader from './fragments/TableHeader.vue'
    import TableRow from './fragments/TableRow.vue'
    import TableFilter from './fragments/TableFilter.vue'
    import OrderDetails from './OrderDetails.vue'

    const {
        activeOrder,
        orders,
        isLoading
    } = inject('useOrders')
</script>