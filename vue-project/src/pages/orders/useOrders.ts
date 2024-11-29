import axios from "axios"
import { onMounted, ref, watch } from "vue"
import { getOrderList } from '@/api'
import { checkCustomer } from '@/remoteApi'

export const useOrders = () => {
    const orders = ref([])
    const activeOrder = ref([])
    const selectedOrders = ref(new Set([]))
    const selectAll = ref(false)
    const isLoading = ref(false)

    const setActiveOrder = (item) => {
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
        if(![...selectedOrders.value].length){
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
            if(data.length){
                data.forEach(item => {
                    _selectedOrders.forEach(_item => {
                        if(item.id == _item.id){
                            _item.customer_report = item.report
                        }
                    })
                })
            }
            console.log(_selectedOrders)
        } finally {
            button.isLoading = false
        }
    }

    watch(() => selectedOrders, (newVal) => {
        selectAll.value = selectedOrders.value.size === orders.value.length
    }, {
        deep: true
    })

    const getOrders = async () => {
        isLoading.value = true
        const { data } = await getOrderList()
        orders.value = data
        isLoading.value = false
    }

    onMounted(() => {
        getOrders()
    })

    return {
        orders,
        selectAll,
        isLoading,
        activeOrder,
        selectedOrders,
        getOrders,
        setActiveOrder,
        toggleSelectAll,
        handleFraudCheck,
    }
}