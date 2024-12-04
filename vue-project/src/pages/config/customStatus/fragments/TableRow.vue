<template>
    <Table.Tr>
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
                :edit="() => toggleEdit = true"
                :delete="(btn) => handleCustomStatusDelete(id, btn)"
            />
        </Table.Td>
    </Table.Tr>

    <Modal 
        v-model="toggleEdit" 
        @close="toggleEdit = false"
        title="Update status"
    >
        <Edit
            :item="item"
            :id="id"
        />
    </Modal>
</template>

<script setup lang="ts">
    import { Table, ThreeDotActionButton, Modal } from '@components'
    import { inject, ref } from 'vue'
    import Edit from './Edit.vue'

    defineProps<{
        item: {
            label: string
            color: string
            description: string
        },
        id: string
    }>()

    const {
        handleCustomStatusDelete
    } = inject('useCustomStatus')

    const toggleEdit = ref(false)
</script>