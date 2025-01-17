import { onMounted, ref, watch } from "vue"
import {
    changeStatus,
    getOrderList,
    getOrderStatusListWithCounts,
    getWoocomerceStatuses,
    ip_or_phone_block_bulk_entry
} from '@/api'
import {
    checkFraudCustomer
} from '@/remoteApi'
import { manageCourier } from "./useHandleCourierEntry"

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
    const alertMessage = ref<{
        title: string
        type: "success" | "danger" | "warning" | "info"
    }>()

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

            const data = await checkFraudCustomer(payload)
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

    const getOrders = async (shouldClear:boolean = true) => {
        try {
            isLoading.value = true
            if (orderFilter.value.page == 0) {
                orderFilter.value.page = 1
            }
            const { data, total } = await getOrderList(orderFilter.value)
            orders.value = data
            totalRecords.value = total
            if(shouldClear){
                selectedOrders.value.clear()
            }
        } finally {
            isLoading.value = false
        }
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

    const handleCourierEntry = async (courierPartner: string, btn) => {
        if (![...selectedOrders.value].length) {
            alert('Please select at least on item.')
            return
        }
        
        try {
            btn.isLoading = true
            await manageCourier(selectedOrders, courierPartner, async () => {
                await getOrders()
                alertMessage.value = {
                    type: 'success',
                    title: 'Your order information has been submitted to the courier platform.'
                }
            })
        } catch ({ response }) {
            const { status, message } = response?.data
            if (!status) {
                alertMessage.value = {
                    type: 'warning',
                    title: message
                }
            }
        } finally {
            btn.isLoading = false
            setTimeout(() => {
                alertMessage.value = {
                    type: 'info',
                    title: ''
                }
            }, 6000)
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
        alertMessage,
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