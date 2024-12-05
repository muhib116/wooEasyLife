<template>
    <Table.Tr
        class="group"
        :active="selectedOrders.has(order) || activeOrder.id == order.id"
        @click="setActiveOrder(order)"  
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
        <Table.Td>{{ order.payment_method_title || 'n/a' }}</Table.Td>
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
    <Table.Tr
        v-if="order?.customer_report"
        class="group relative !bg-white !hover:bg-white"
    >
        <span class="absolute size-4 border-r border-b border-green-400 bg-green-50 rotate-45 -mt-2 left-1/2"></span>
        <Table.Td colspan="8" class="text-center text-lg ">
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
        activeOrder,
        setActiveOrder,
        selectedOrders
    } = inject('useOrders')

    const toggleModel = ref(false)
    const toggleFraudHistoryModel = ref(false)
</script>