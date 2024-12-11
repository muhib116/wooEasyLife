import axios from "axios"
import { onMounted, ref, watch } from "vue"
import { getOrderList, getOrderStatusListWithCounts } from '@/api'
import { checkCustomer } from '@/remoteApi'

export const useOrders = () => {
    const orders = ref([])
    const orderStatusWithCounts = ref([])
    const activeOrder = ref()
    const selectedOrders = ref(new Set([]))
    const selectAll = ref(false)
    const isLoading = ref(false)
    const orderFilter = ref({
        page: 1,
        per_page: 10,
        status: ''
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
        const { data } = await getOrderList(orderFilter.value)
        orders.value = data
        isLoading.value = false
    }

    const loadOrderStatusList = async () => {
        isLoading.value = true
        const { data } = await getOrderStatusListWithCounts()
        orderStatusWithCounts.value = data
        isLoading.value = false
    }


    watch(() => selectedOrders, (newVal) => {
        selectAll.value = selectedOrders.value.size === orders.value.length
    }, {
        deep: true
    })
    onMounted(() => {
        loadOrderStatusList();
        getOrders()
    })

    return {
        orders,
        selectAll,
        isLoading,
        activeOrder,
        selectedOrders,
        orderStatusWithCounts,
        getOrders,
        orderFilter,
        setActiveOrder,
        setSelectedOrder,
        toggleSelectAll,
        handleFraudCheck,
        loadOrderStatusList,
    }
}