<template>
    <div v-bind="$attrs" class="flex justify-between text-[10px] px-4 my-4">
        <div class="flex-justify-controller">
            <div class="flex gap-3 items-center">
                <span 
                    class="size-6 shadow -mr-2 flex items-center justify-center text-[10px] aspect-square rounded-full bg-orange-500 text-white "
                >
                    {{ [...selectedOrders].length }}
                </span>
                <template
                    v-for="(item, index) in actionBtns"
                    :key="index"
                >
                    <Button.Native
                        v-if="item.active"
                        class="py-1 px-2 border shadow rounded-sm"
                        :style="{
                            backgroundColor: item.bg,
                            color: item.color   
                        }"
                        @onClick="item.method"
                    >
                        <Icon
                            :name="item.icon"
                            size="16"
                        />
                        {{ item.title }}
                    </Button.Native>
                </template>
            </div>
        </div>
        <slot></slot>
    </div>

    <Modal
        v-model="toggleNewOrder"
        title="Create New Order"
        @close="toggleNewOrder = false"
        class="max-w-[650px] w-full"
        hideFooter
    >
        <CreateNewOrder />
    </Modal>
</template>

<script setup lang="ts">
    import { inject, computed, ref } from 'vue'
    import CreateNewOrder from './createNewOrder/Index.vue'
    import {
        Button,
        Icon,
        Modal
    } from '@components'

    defineOptions({
        inheritAttrs: false
    })
    
    const {configData} = inject('configData')
    const { 
        handleFraudCheck, 
        handleCourierEntry,
        handlePhoneNumberBlock, 
        handleIPBlock,
        selectedOrders,
        showInvoices,
        toggleNewOrder,
    } = inject('useOrders')

    const actionBtns = computed(() => [
        {
            icon: 'PhPlus',
            title: 'Create New Order',
            active: true,
            bg: '#155E95',
            color: '#fff',
            method: () => {
                toggleNewOrder.value = true
            }
        },
        {
            icon: 'PhPrinter',
            title: 'Print Invoice',
            active: configData.value.invoice_print,
            bg: '#16404D',
            color: '#fff',
            method: () => {
                showInvoices.value = true
            }
        },
        {
            icon: 'PhTruck',
            title: 'Courier Entry',
            bg: '#553555',
            color: '#fff',
            active: configData.value.courier_automation,
            method: handleCourierEntry
        },
        {
            icon: 'PhNetworkSlash',
            title: 'Block IP',
            active: configData.value.ip_block,
            bg: '#F93827',
            color: '#fff',
            method: handleIPBlock
        },
        {
            icon: 'PhSimCard',
            title: 'Block Phone',
            active: configData.value.phone_number_block,
            bg: '#E82561',
            color: '#fff',
            method: handlePhoneNumberBlock
        },
        {
            icon: 'PhUserList',
            title: 'Fraud Check',
            active: configData.value.fraud_customer_checker,
            bg: '#F14A00',
            color: '#fff',
            method: handleFraudCheck
        },
    ])
</script>