<template>
    <div class="relative">
        <Loader
            class="absolute top-[30vh] left-1/2 -translate-x-1/2 z-30"
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
            <Pagination />

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

            <MessageBox
                v-else
                title="No record found!"
                type="info"
                class="mx-4"
            />

            <TableHeaderAction class="items-center">
                <Pagination
                    hideSearch
                />
            </TableHeaderAction>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Table, Loader, Heading, MessageBox } from '@components'
    import { inject } from 'vue'
    import TableHeaderAction from './TableHeaderAction.vue'
    import TableHeader from './fragments/TableHeader.vue'
    import TableRow from './fragments/TableRow.vue'
    import TableFilter from './fragments/TableFilter.vue'
    import OrderDetails from './OrderDetails.vue'
    import Pagination from './fragments/Pagination.vue'

    const {
        activeOrder,
        orders,
        isLoading
    } = inject('useOrders')
</script>