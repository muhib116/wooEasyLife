<template>
    <Card.Native class="relative h-[390px] overflow-auto">
        <Heading
            title="Recent Orders"
            class="mb-2"
        />
        <Loader
            class="absolute left-1/2 -translate-x-1/2 top-[200px] z-40"
            :active="isLoading"
        />

        <MessageBox
            v-if="!recentOrders?.length && !isLoading"
            title="No records found for the top-selling product!"
            type="info"
        />
        <Table.Table v-else-if="!isLoading">
            <Table.THead class="whitespace-nowrap">
                <Table.Th>Order Info</Table.Th>
                <Table.Th>Delivery History</Table.Th>
                <Table.Th>Delivery Partner</Table.Th>
                <Table.Th>Shipping</Table.Th>
                <Table.Th>Payment</Table.Th>
                <Table.Th>Status</Table.Th>
                <Table.Th>Note</Table.Th>
                <Table.Th>Address</Table.Th>
                <Table.Th>Action</Table.Th>
            </Table.THead>
            <Table.TBody>
                <TableRow 
                    v-for="order in recentOrders"
                    :key="order.id"
                    :order="order"
                />
            </Table.TBody>
        </Table.Table>
    </Card.Native>
</template>

<script setup lang="ts">
    import { Card, Table, Loader, Heading, MessageBox } from '@components'
    import { useRecentOrder } from './UseRecentOrder'
    import TableRow from './TableRow.vue'

    const {
        isLoading,
        recentOrders
    } = useRecentOrder()
</script>