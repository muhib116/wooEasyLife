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
    <div 
        v-else
        class="text-center text-gray-300 flex flex-col items-center gap-4"
    >
        No custom status available
        <Button.Primary
            class="px-auto animate-bounce"
            @click="tabChange('create')"
        >
            Create custom order status
        </Button.Primary>
    </div>
</template>

<script setup lang="ts">
    import { inject } from 'vue'
    import { Table, Heading, Button } from '@components'
    import TableRow from './TableRow.vue'

    const {
        statusList,
        tabChange
    } = inject('useCustomStatus')
</script>