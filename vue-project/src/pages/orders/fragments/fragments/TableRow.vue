<template>
    <Table.Tr
        class="group"
        :class="`status-${order.status}`"
        :active="selectedOrders.has(order)"
    >
        <Table.Td
            @click="setSelectedOrder(order)"  
        >
            <input
                type="checkbox"
                :value="order.id"
                :checked="selectedOrders.has(order)"
            />
        </Table.Td>
        <Table.Td
            @click="setSelectedOrder(order)"  
        >
            <div
                class="flex gap-1"
            >
                #{{ order.id }}
                {{ order.billing_address.first_name }}
                {{ order.billing_address.last_name }}
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
            <div class="flex gap-2">
                <span
                    v-if="order.ip_block_listed"
                    class="!py-0 !text-[10px] flex items-center text-red-500"
                >
                    <Icon
                        name="PhCellTower"
                        size="12"
                    />
                    Ip blocked
                </span>
                <span
                    v-if="order.phone_block_listed"
                    class="!py-0 !text-[10px] flex items-center text-red-500"
                >
                    <Icon
                        name="PhSimCard"
                        size="12"
                    />
                    Phone blocked
                </span>
            </div>
        </Table.Td>
        <Table.Td
            @click="setSelectedOrder(order)"
        >
            <div class="w-[100px] text-center">
                {{ order.date_created }}
            </div>
        </Table.Td>
        <Table.Td
            @click="setSelectedOrder(order)"  
        >
            <button class="order-status capitalize px-3" :class="`status-${order.status}`">
                {{ order.status=='processing' ? 'New Order' : order.status.replaceAll('-', ' ') }}
            </button>
        </Table.Td>
        <Table.Td
            @click="setSelectedOrder(order)"  
        >
            {{ order.payment_method_title || 'n/a' }}
        </Table.Td>
        <Table.Td
            @click="setSelectedOrder(order)"
        >
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
        <Table.Td class="pointer-events-none">
            <button
                class="relative flex flex-col whitespace-nowrap justify-center items-center text-blue-500 pointer-events-auto"
                title="Order details"
                @click="(e) => {
                    e.preventDefault();
                    setActiveOrder(order)
                }"
            >
                <Icon
                    name="PhFileText"
                    size="20"
                />
                Details
            </button>
        </Table.Td>
    </Table.Tr>
    <Table.Tr
        v-if="order?.customer_report"
        class="group relative !bg-white !hover:bg-white"
    >
        <span 
            class="absolute size-3 border-r border-b rotate-45 -mt-[6px] left-1/2"
            :class="selectedOrders.has(order) ? 'border-green-400 bg-green-50' : 'bg-white'"
        ></span>
        <Table.Td colspan="8" class="text-center">
            <div class="flex gap-6">
                <div class="text-green-500">
                    🎉 Total confirm order: 
                    <strong>{{ order.customer_report?.confirmed }}</strong><br/>
                </div>
                <div class="text-red-500">
                    ❌ Total cancel order: 
                    <strong>{{ order.customer_report?.total_order - order.customer_report?.confirmed }}</strong>
                </div>
                <div class="text-blue-500">
                    ✅ Success Rate: 
                    <strong>
                        {{ order.customer_report?.success_rate }}
                    </strong>

                </div>
                <Button.Outline
                    @click="toggleFraudHistoryModel=true"
                    class="py-0.5 font-light text-orange-500 ml-auto"
                >
                    See details
                </Button.Outline>
            </div>
        </Table.Td>
    </Table.Tr>

    <Modal 
        v-model="toggleModel"
        @close="toggleModel = false"
        class="max-w-[70%] w-full"
        title="Address manage"
    >
        <Address :order="order" />
    </Modal>

    <Modal 
        v-model="toggleFraudHistoryModel"
        @close="toggleFraudHistoryModel = false"
        class="max-w-[50%] w-full"
        :title="`Fraud history`"
    >
        <FraudHistory
            :order="order"
        />
    </Modal>
</template>

<script setup lang="ts">
    import { Table, Icon, Badge, Modal, Button } from '@components'
    import { inject, ref } from 'vue'
    import Address from './address/Index.vue'
    import { baseUrl } from '@/api'
    import FraudHistory from './FraudHistory.vue'

    defineProps<{
        order: object
    }>()

    const {
        setActiveOrder,
        setSelectedOrder,
        selectedOrders
    } = inject('useOrders')

    const toggleModel = ref(false)
    const toggleFraudHistoryModel = ref(false)
</script>