import { onMounted, ref, watch } from "vue"
import { 
    changeStatus, 
    getOrderList, 
    getOrderStatusListWithCounts, 
    getWoocomerceStatuses, 
    ip_or_phone_block_bulk_entry
} from '@/api'

import {
    storeBulkRecordsInToOrdersMeta
} from '@/api/courier'
import { 
    checkCustomer,
    steadfastOrderCreate,
} from '@/remoteApi'
import { normalizePhoneNumber } from "@/helper"

export const useOrders = () => {
    const orders = ref([])
    const totalRecords = ref(0)
    const orderStatusWithCounts = ref([])
    const activeOrder = ref()
    const selectedOrders = ref(new Set([]))
    const selectAll = ref(false)
    const isLoading = ref(false)
    const showInvoices = ref(false)
    const toggleNewOrder = ref(false)
    const wooCommerceStatuses = ref([])
    const selectedStatus = ref(null)

    const orderFilter = ref({
        page: 1,
        per_page: 30,
        status: '',
        search: ''
    })

    const setActiveOrder = (item) => {
        activeOrder.value = item
    }
    const setSelectedOrder = (item) => {
        if (!selectedOrders.value.has(item)) {
            selectedOrders.value.add(item)
        } else {
            selectedOrders.value.delete(item)
        }
    }

    const toggleSelectAll = () => {
        if (selectAll.value) {
            selectedOrders.value = new Set(orders.value)
        } else {
            selectedOrders.value.clear()
        }
    }

    const handleFraudCheck = async (button) => {
        if (![...selectedOrders.value].length) {
            alert('Please select at least on item.')
            return
        }

        const _selectedOrders = [...selectedOrders.value]
        try {
            button.isLoading = true
            const payload = {
                phone: _selectedOrders.map(item => {
                    return {
                        id: item.id,
                        phone: item.billing_address.phone
                    }
                }),
            }

            const data = await checkCustomer(payload)
            if (data.length) {
                data.forEach(item => {
                    _selectedOrders.forEach(_item => {
                        if (item.id == _item.id) {
                            _item.customer_report = item.report
                        }
                    })
                })
            }
        } finally {
            button.isLoading = false
        }
    }

    const getOrders = async () => {
        isLoading.value = true
        if(orderFilter.value.page==0){
            orderFilter.value.page = 1
        }
        const { data, total } = await getOrderList(orderFilter.value)
        orders.value = data
        totalRecords.value = total
        selectedOrders.value.clear()
        isLoading.value = false
    }

    const loadAllStatuses = async () => {
        try {
            isLoading.value = true
            const { data } = await getWoocomerceStatuses()
            wooCommerceStatuses.value = data
        } finally {
            isLoading.value = false
        }
    }

    const loadOrderStatusList = async () => {
        isLoading.value = true
        const { data } = await getOrderStatusListWithCounts()
        orderStatusWithCounts.value = data
        isLoading.value = false
    }

    const handlePhoneNumberBlock = async (btn) => {
        if (![...selectedOrders.value].length) {
            alert('Please select at least on item.')
            return
        }

        const payload: {
            type: 'phone_number',
            ip_or_phone: string
        }[] = [...selectedOrders.value].map(item => ({
            type: 'phone_number',
            ip_or_phone: item?.billing_address?.phone
        }))

        try {
            btn.isLoading = true
            await ip_or_phone_block_bulk_entry(payload)
            await getOrders()
        } finally {
            btn.isLoading = false
        }
    }

    const handleIPBlock = async (btn) => {
        if (![...selectedOrders.value].length) {
            alert('Please select at least on item.')
            return
        }

        const payload: {
            type: 'ip',
            ip_or_phone: string
        }[] = [...selectedOrders.value].map(item => ({
            type: 'ip',
            ip_or_phone: item?.customer_ip
        }))

        try {
            btn.isLoading = true
            await ip_or_phone_block_bulk_entry(payload)
            await getOrders()
        } finally {
            btn.isLoading = false
        }
    }

    const handleStatusChange = async (btn) => {
        if (![...selectedOrders.value].length) {
            alert('Please select at least on item.')
            return
        }

        if (!selectedStatus.value) {
            alert("Please select status from dropdown.")
        }

        try {
            btn.isLoading = true
            const payload: {
                order_id: number
                new_status: string
            }[] = [...selectedOrders.value].map(item => ({
                new_status: selectedStatus.value,
                order_id: item?.id
            }))

            await changeStatus(payload)
            loadOrderStatusList()
            await getOrders()
        } catch (err) {
            console.log(err)
        } finally {
            btn.isLoading = false
        }
    }

    const handleCourierEntry = async (btn) => {
        if (![...selectedOrders.value].length) {
            alert('Please select at least on item.')
            return
        }

        try {
            btn.isLoading = true
            const payload: {
                orders: {
                    invoice: number | string
                    recipient_name: string
                    recipient_phone: string
                    recipient_address: string
                    cod_amount: number | string
                }[]
            } = {
                orders: [...selectedOrders.value]
                        .map(item => ({
                            invoice: item?.id,
                            recipient_name: item?.customer_name || '',
                            recipient_phone: normalizePhoneNumber(item?.billing_address?.phone || item?.shipping_address?.phone || ''),
                            recipient_address: `${item?.shipping_address?.address_1 || ''} ${item?.shipping_address?.address_2 || ''}`,
                            cod_amount: item?.total,
                            note: item?.order_notes?.courier_note || '',
                        }))
            }

            console.log(payload)

            const { data } = await steadfastOrderCreate(payload)
            console.log(data);
            // after getting courier response store it into DB
            storeBulkRecordsInToOrdersMeta([]);
            await getOrders()
        } catch (err) {
            console.log(err)
        } finally {
            btn.isLoading = false
        }
    }


    watch(() => selectedOrders, (newVal) => {
        selectAll.value = selectedOrders.value.size === orders.value.length
    }, {
        deep: true
    })
    onMounted(() => {
        loadOrderStatusList()
        loadAllStatuses()
        getOrders()
    })

    return {
        orders,
        selectAll,
        isLoading,
        activeOrder,
        orderFilter,
        showInvoices,
        totalRecords,
        selectedStatus,
        selectedOrders,
        toggleNewOrder,
        wooCommerceStatuses,
        orderStatusWithCounts,
        getOrders,
        handleIPBlock,
        setActiveOrder,
        setSelectedOrder,
        toggleSelectAll,
        handleFraudCheck,
        handleStatusChange,
        handleCourierEntry,
        loadOrderStatusList,
        handlePhoneNumberBlock,
    }
}