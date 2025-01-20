<template>
    <Table.Table>
        <Table.THead>
            <Table.Th>Customer Info</Table.Th>
            <Table.Th>Contact Info</Table.Th>
            <Table.Th>Status Of Cart</Table.Th>
            <Table.Th>Order Details</Table.Th>
        </Table.THead>
        <Table.TBody>
            <template v-if="filteredAbandonOrders?.length">
                <TableRow
                    v-for="item in filteredAbandonOrders || []"
                    :key="item.id"
                    :item="item"
                />
            </template>
            <Table.Tr v-else>
                <Table.Td colspan="4" class="text-center text-gray-600 text-lg">
                    No result found!
                </Table.Td>
            </Table.Tr>
        </Table.TBody>
    </Table.Table>
</template>

<script setup lang="ts">
    import { Table } from '@components'
    import { inject } from 'vue'
    import TableRow from './TableRow.vue'

    const {
        selectedFilter,
        filteredAbandonOrders
    } = inject('useMissingOrder')
</script>