<template>
    <Table.Table>
        <Table.THead>
            <Table.Th>#SL</Table.Th>
            <Table.Th>Status</Table.Th>
            <Table.Th>Message</Table.Th>
            <Table.Th>Action</Table.Th>
        </Table.THead>
        <Table.TBody>
            <Table.Tr>
                <Table.Td>1</Table.Td>
                <Table.Td>Success</Table.Td>
                <Table.Td>
                    Hello world, lorem ipsum dollar
                </Table.Td>
                <Table.Td>
                    Edit
                </Table.Td>
            </Table.Tr>
        </Table.TBody>
    </Table.Table>
</template>

<script setup lang="ts">
    import { Table } from '@components'
</script>