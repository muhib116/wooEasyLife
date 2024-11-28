import axios from "axios"
import { onMounted, ref, watch } from "vue"
import { getOrderList } from '@/api'

export const useOrders = () => {
    const orders = ref([])
    const activeOrder = ref([])
    const selectedOrders = ref(new Set([]))
    const selectAll = ref(false)

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

    watch(() => selectedOrders, (newVal) => {
        selectAll.value = selectedOrders.value.size === orders.value.length
    }, {
        deep: true
    })

    onMounted(async () => {
        const { data } = await getOrderList()
        orders.value = data
    })

    return {
        orders,
        selectAll,
        activeOrder,
        setActiveOrder,
        selectedOrders,
        toggleSelectAll,
    }
}