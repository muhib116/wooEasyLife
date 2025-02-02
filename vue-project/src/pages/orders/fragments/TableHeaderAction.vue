<template>
    <div v-bind="$attrs" class="flex justify-between text-[10px] px-4 my-4">
        <div 
            class="flex gap-3 items-center relative z-40"
            v-click-outside="() => toggleCourierDropdown = false"
        >
            <span 
                class="size-6 shadow -mr-2 flex items-center justify-center text-[10px] aspect-square rounded-full bg-orange-500 text-white "
            >
                {{ [...selectedOrders].length }}
            </span>
            <template
                v-for="(item, index) in actionBtns"
                :key="index"
            >
                <div class="relative"
                    v-if="item.active"
                >
                    <Button.Native
                        :title="item.title"
                        class="py-1 px-2 border shadow rounded-sm"
                        :style="{
                            backgroundColor: item.bg,
                            color: item.color   
                        }"
                        @onClick="btn => item?.isCourier 
                                        ? toggleCourierDropdown = !toggleCourierDropdown 
                                        : item.method(btn)"
                    >
                        <Icon
                            :name="item.icon"
                            size="16"
                        />
                        {{ item.title }}
                    </Button.Native>
                    <div
                        v-if="item?.isCourier && toggleCourierDropdown"
                        class="absolute top-full left-0 min-w-[120px] border border-[#693d84] overflow-hidden bg-white [&>button+button]:border-t shadow rounded-b-sm z-50 grid"
                    >
                        <template
                            v-for="_item in courierCompanyNames"
                            :key="_item.slug"
                        >
                            <Button.Native 
                                v-if="courierConfigs[_item.slug]?.is_active"
                                class="text-left text-xl px-2 py-2 text-gray-500 hover:scale-110 origin-left duration-300"
                                @onClick="btn => item.method(_item.slug, btn)"
                            >
                                <img
                                    v-if="courierConfigs[_item.slug]?.logo"
                                    :src="courierConfigs[_item.slug]?.logo"
                                    class="w-20 object-contain"
                                />
                                <span v-else>{{ _item.title }}</span>
                            </Button.Native>
                        </template>
                    </div>
                </div>
            </template>

            <Button.Native
                class="opacity-100 w-fit text-white bg-sky-500 shadow rounded-sm px-1 py-1"
                title="Refresh CourierData"
                @onClick="refreshBulkCourierData"
            >
                <Icon
                    name="PhArrowsClockwise"
                    size="16"
                    weight="bold"
                />
                Courier Status
            </Button.Native>

            <Button.Native
                class="opacity-100 w-fit text-white bg-sky-500 shadow rounded-sm px-1 py-1"
                title="Refresh CourierData"
                @onClick="refreshBulkCourierData"
            >
                <Icon
                    name="PhArrowSquareIn"
                    size="16"
                    weight="bold"
                    class="rotate-[180deg]"
                />
                Import previous new order
            </Button.Native>


        </div>

        <div>
            <slot></slot>
        </div>
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
    
    const toggleCourierDropdown = ref(false)
    const {configData} = inject('configData')
    const {
        courierCompanyNames,
        courierConfigs
    } = inject('useCourierConfig')


    const { 
        handleFraudCheck, 
        handleCourierEntry,
        handlePhoneNumberBlock, 
        handleEmailBlock, 
        handleIPBlock,
        selectedOrders,
        showInvoices,
        toggleNewOrder,
        refreshBulkCourierData
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
            isCourier: true,
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
            icon: 'PhEnvelopeSimple',
            title: 'Block Email',
            active: configData.value.email_block,
            bg: '#444',
            color: '#fff',
            method: handleEmailBlock
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