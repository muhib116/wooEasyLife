<template>
    <Card.Native class="relative min-h-[200px]">
        <Loader
            :active="isLoading"
            class="absolute inset-x-1/2 top-24 -translate-x-1/2 z-20"
        />
        <MessageBox
            :title="alertMessage.message"
            :type="alertMessage.type"
        />
        <div class="mb-2">
            <h3 class="text-xl font-semibold text-gray-900">Configure your preference</h3>
        </div>
        <Table.Table>
            <Table.THead>
                <Table.Th>Config Name</Table.Th>
                <Table.Th>on/off</Table.Th>
            </Table.THead>
            <Table.TBody>
                <template
                    v-for="(item, key) in configData"
                    :key="key"
                >
                    <TableTrow
                        :objKey="key"
                    />
                </template>
            </Table.TBody>
        </Table.Table>
    </Card.Native>
</template>


<script setup>
    import { Table, MessageBox, Loader, Card } from '@components'
    import { useIntegration } from './useIntegration'
    import { provide } from 'vue'
    import TableTrow from './fragment/TableRow.vue'

    const _useIntegration = useIntegration()
    const {
        isLoading,
        configData,
        alertMessage,
    } = _useIntegration

    provide('useIntegration', _useIntegration)
</script>