<template>
    <Table.Tr>
        <Table.Td>
            <div class="flex gap-2 truncate">
                <span 
                    class="px-1 bg-gray-500 text-white capitalize rounded-sm text" 
                    title="Order Id"
                >
                    #{{ item.id }}
                </span>
                <span 
                    v-if="item.is_repeat_customer"
                    class="px-1 bg-sky-500 text-white capitalize rounded-sm text" 
                    title="Repeat customer"
                >
                    Repeat
                </span>
            </div>
            <div class="flex gap-1 font-semibold" title="Customer name">
                {{ item.customer_name }}
            </div>
            📅 {{ printDate(item.created_at) }}
        </Table.Td>
        <Table.Td class="space-y-2">
            <div>
                <span class="font-semibold text-[#02b795]">
                    📞 Phone:
                </span> 
                {{ item.customer_phone }}
            </div>

            <div v-if="item.customer_email">
                <span class="font-semibold text-orange-500">
                    Email:
                </span> 
                {{ item.customer_email }}
            </div>
            

            <div>
                🏠 <span class="font-semibold text-sky-500">Billing address:</span> 
                {{ item.billing_address }}
            </div>

            <div>
                📍 <span class="font-semibold text-red-500">Shipping address:</span> 
                {{ item.shipping_address }}
            </div>
        </Table.Td>
        <Table.Td class="capitalize">
            <span 
                class="text-white px-2 py-1 rounded-sm"
                :class="item.status == 'abandoned' ? 'bg-red-500' : 'bg-green-500'"
            >
                {{ item.status }}
            </span>
        </Table.Td>
    </Table.Tr>
</template>

<script setup lang="ts">
    import { printDate } from '@/helper'
    import {
        Table
    } from '@components'

    defineProps<{
        item: object
    }>()
</script>