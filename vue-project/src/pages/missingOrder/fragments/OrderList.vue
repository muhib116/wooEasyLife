<template>
    <Table.Table>
        <Table.THead>
            <Table.Th>ID</Table.Th>
            <Table.Th>Date</Table.Th>
            <Table.Th>Name</Table.Th>
            <Table.Th>Phone</Table.Th>
            <Table.Th>Email</Table.Th>
            <Table.Th>Address</Table.Th>
            <Table.Th>Status Of Cart</Table.Th>
            <Table.Th>Order Details</Table.Th>
        </Table.THead>
        <Table.TBody>
            <TableRow
                v-for="item in abandonOrders || []"
                :key="item.id"
                :item="item"
            />
        </Table.TBody>
    </Table.Table>
</template>

<script setup lang="ts">
    import { Table } from '@components'
    import { inject } from 'vue'
    import TableRow from './TableRow.vue'

    const {
        abandonOrders
    } = inject('useMissingOrder')
</script>