import { getOrderStatusStatistics } from "@/api/dashboard"
import { onMounted, ref } from "vue"
import { getOrderStatuses } from "@/api"

export const useStatusStatistics = () => {
    const statusData = ref([])
    const isLoading = ref(false)
    const chartKey = ref(Date.now()) // Unique key
    const orderStatuses = ref([])
    const orderStatistics = ref({})

    const loadOrderStatuses = async () => {
        const { data } = await getOrderStatuses()
        orderStatuses.value = data.map(item => {
            return {
                title: item.title,
                slug: item.slug
            }
        })
    }
    
    const loadOrderStatisticsData = async (date: {start_date: string, end_date: string}) => {
        try {
            isLoading.value = true
            const { data } = await getOrderStatusStatistics(date)
            orderStatistics.value = data
        } finally {
            isLoading.value = false
        }
    }

    onMounted(async () => {
        await loadOrderStatuses()
    })

    return {
        chartKey,
        isLoading,
        orderStatuses,
        orderStatistics,
        loadOrderStatisticsData
    }
}