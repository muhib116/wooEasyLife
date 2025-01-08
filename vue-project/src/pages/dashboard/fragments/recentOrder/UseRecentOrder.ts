import { getRecentOrders } from "@/api/dashboard"
import { onMounted, ref } from "vue"

export const useRecentOrder = () => {
    const recentOrders = ref([])
    const isLoading = ref(false)
    const loadRecentOrders = async (limit:number=10) => {
        try {
            isLoading.value = true
            const data = await getRecentOrders(limit)
            recentOrders.value = data
        } finally {
            isLoading.value = false
        }
    }

    onMounted(() => {
        loadRecentOrders()
    })
    return {
        isLoading,
        recentOrders,
        loadRecentOrders   
    }
}