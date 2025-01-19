<template>
    <Table.Tr>
        <Table.Td>
            {{ item.id }}
        </Table.Td>
        <Table.Td>
            {{ item.created_at }}
        </Table.Td>
        <Table.Td>
            {{ item.customer_name }}
        </Table.Td>
        <Table.Td>
            {{ item.customer_phone }}
        </Table.Td>
        <Table.Td>
            {{ item.customer_email }}
        </Table.Td>
        <Table.Td>
            {{ item }}
        </Table.Td>
    </Table.Tr>
</template>

<script setup lang="ts">
    import {
        Table
    } from '@components'

    defineProps<{
        item: object
    }>()
</script>