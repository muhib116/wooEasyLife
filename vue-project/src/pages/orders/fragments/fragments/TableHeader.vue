<template>
    <Table.THead class="truncate bg-gray-700 text-white">
        <Table.Th>
            <label class="flex gap-2">
                <input
                    type="checkbox"
                    v-model="selectAll"
                    @change="toggleSelectAll"
                    title="Click here to select all orders"
                />
            </label>
        </Table.Th>
        <Table.Th>Customer</Table.Th>
        <Table.Th>Delivery History</Table.Th>
        <Table.Th>Status</Table.Th>
        <Table.Th>Payment Method</Table.Th>
        <Table.Th>Price</Table.Th>
        <Table.Th>Address</Table.Th>
        <Table.Th>Action</Table.Th>
    </Table.THead>
</template>

<script setup lang="ts">
    import { Table } from '@components'
    import { inject } from 'vue'


    const {
        selectAll,
        toggleSelectAll
    } = inject('useOrders')
</script>