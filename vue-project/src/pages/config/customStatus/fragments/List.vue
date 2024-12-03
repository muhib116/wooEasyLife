<template>
    <div class="relative">
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
                <Table.Tr
                    v-for="(item, index) in statusList"
                    :key="index"
                >
                    <Table.Td>{{ item.label }}</Table.Td>
                    <Table.Td>{{ item.description }}</Table.Td>
                    <Table.Td>
                        <span 
                            class="block mx-auto size-8 rounded-full border-2 shadow cursor-pointer border-white" 
                            :style="{backgroundColor: item.color}"
                        ></span>
                    </Table.Td>
                    <Table.Td class="text-center">
                        <ThreeDotActionButton
                            :edit="(btn:object) => {
                                console.log(btn, 'edit')
                            }"
                            :delete="(btn:object) => {
                                console.log(btn, 'delete')
                            }"
                        />
                    </Table.Td>
                </Table.Tr>
            </Table.TBody>
        </Table.Table>
    </div>
</template>

<script setup lang="ts">
    import { inject } from 'vue'
    import { Table, ThreeDotActionButton, Heading } from '@components'

    const {
        statusList
    } = inject('useCustomStatus')
</script>