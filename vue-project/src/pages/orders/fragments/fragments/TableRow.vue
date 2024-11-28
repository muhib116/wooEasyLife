<template>
    <Table.Tr
        class="group"
        :active="selectedOrders.has(order) || activeOrder.id == order.id"
    >
        <Table.Td>
            <input
                type="checkbox"
                :value="order.id"
                @change="() => setActiveOrder(order)"
                :checked="selectedOrders.has(order)"
            />
        </Table.Td>
        <Table.Td>
            <div
                @click="setActiveOrder(order)"
                class="flex gap-1"
            >
                #{{ order.id }}
                {{ order.customer_name }}
                <a 
                    class="text-orange-500 hover:scale-150 duration-200 opacity-0 group-hover:opacity-100"
                    :href="`${baseUrl}/wp-admin/post.php?post=${order.id}&action=edit`"
                    target="_blank"
                >
                    <Icon 
                        name="PhArrowSquareOut"
                        size="20"
                        weight="bold"
                    />
                </a>                
            </div>
        </Table.Td>
        <Table.Td>
            <div class="w-[100px] text-center">
                {{ order.date_created }}
            </div>
        </Table.Td>
        <Table.Td>
            <Badge.Native>{{ order.status }}</Badge.Native>
        </Table.Td>
        <Table.Td>{{ order.payment_method_title }}</Table.Td>
        <Table.Td>
            <div v-html="order.product_price"></div>
        </Table.Td>
        <Table.Td>
            <button
                class="relative flex flex-col whitespace-nowrap justify-center items-center text-red-500"
                @click="toggleModel = true"
            >
                <Icon
                    name="PhMapTrifold"
                    size="20"
                />
                Address
            </button>
        </Table.Td>
        <Table.Td>
            <button
                class="relative flex flex-col whitespace-nowrap justify-center items-center text-blue-500"
                title="Order details"
            >
                <Icon
                    name="PhFileText"
                    size="20"
                />
                Details
            </button>
        </Table.Td>
    </Table.Tr>

    <Modal 
        v-model="toggleModel"
        @close="toggleModel = false"
        class="max-w-[70%] w-full"
        title="Address manage"
        confirmText="Save"
    >
        <Address :order="order" />
    </Modal>
</template>

<script setup lang="ts">
    import { Table, Icon, Badge, Modal } from '@components'
    import { inject, ref } from 'vue'
    import Address from './address/Index.vue'
    import { baseUrl } from '@/api'

    defineProps<{
        order: object
    }>()

    const {
        activeOrder,
        setActiveOrder,
        selectedOrders
    } = inject('useOrders')

    const toggleModel = ref(false)
</script>