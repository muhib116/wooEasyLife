<template>
    <div v-if="Object.keys(statusList).length" class="relative">
        <Heading
            title="Status list"
            class="mb-2"
        />
        <Table.Table>
            <Table.THead class="truncate">
                <Table.Th>Title</Table.Th>
                <Table.Th>Description</Table.Th>
                <Table.Th class="text-center">Color</Table.Th>
                <Table.Th class="text-center">Actions</Table.Th>
            </Table.THead>
            <Table.TBody>
                <TableRow
                    v-for="(item: object, id: string) in statusList"
                    :key="id"
                    :item="item"
                    :id="id"
                />
            </Table.TBody>
        </Table.Table>
    </div>
</template>

<script setup lang="ts">
    import { inject } from 'vue'
    import { Table, Heading } from '@components'
    import TableRow from './TableRow.vue'

    const {
        statusList,
    } = inject('useCustomStatus')
</script>