<template>
<div>
    <h3 class="font-semibold text-lg">
        Name: 
        {{ order.customer_name }}
    </h3>
    <h3>{{ getContactInfo }}</h3>

    <Table.Table class="mt-4">
        <Table.THead>
            <Table.Th>Courier Name</Table.Th>
            <Table.Th class="text-center">Confirm</Table.Th>
            <Table.Th class="text-center">Cancel</Table.Th>
            <Table.Th class="text-center">Success Rate</Table.Th>
        </Table.Thead>
        <Table.TBody>
            <Table.Tr
                v-for="item in order.customer_report.courier"
            >
                <Table.Th>{{ item.title }}</Table.Th>
                <Table.Th class="text-center">{{ item.report.confirmed }}</Table.Th>
                <Table.Th class="text-center">{{ item.report.cancel }}</Table.Th>
                <Table.Th class="text-center">{{ item.report.success_rate }}</Table.Th>
            </Table.Tr>
        </Table.TBody>
        <Table.THead>
            <Table.Th>Total</Table.Th>
            <Table.Th class="bg-green-500 text-white text-center">
                {{ order.customer_report.confirmed }}
            </Table.Th>
            <Table.Th class="bg-red-500 text-white text-center">
                {{ order.customer_report.cancel }}
            </Table.Th>
            <Table.Th class="bg-sky-500 text-white text-center">
                {{ order.customer_report.success_rate }}
            </Table.Th>
        </Table.Thead> 
    </Table.Table>
</div>
</template>

<script setup lang="ts">
    import { Heading, Table } from '@components'
    import { computed } from 'vue'

    const props = defineProps<{
        order: object
    }>()

    const getContactInfo = computed(() => {
        if(!props.order?.billing_address) return ''

        const { phone, email } = props.order.billing_address
        let data = phone ? `Phone: ${phone}` : ''
            data += email ? ` | Email: ${email}` : ''
        return data
    })
</script>