<template>
<Table.Tr>
    <Table.Td class="capitalize">
        {{ index + 1 }}
    </Table.Td>
    <Table.Td class="capitalize">{{ objKey.replaceAll('_', ' ') }}</Table.Td>
    <Table.Td>
        <Switch
            v-if="typeof configData[objKey] == 'boolean'"
            v-model="configData[objKey]"
            @onInput="UpdateConfig"
        />
        <Input.Primary
            wrapperClass="w-20"
            type="number"
            v-else
            v-model="configData[objKey]"
            @input="UpdateConfig"
        />
    </Table.Td>
</Table.Tr>
</template>

<script setup lang="ts">
    import { Table, Input, Switch } from '@components'
    import { inject } from 'vue'

    const {
        configData,
        UpdateConfig
    } = inject('useIntegration')

    defineProps<{
        index: number,
        objKey: string,
    }>()
</script>