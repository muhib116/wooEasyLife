import { getSalesProgressData } from "@/api/dashboard"
import { inject, onMounted, provide, ref } from "vue"

export const useSalesProgress = () => {
    const {} = inject('useDashboard')
    
    const salesProgressData = ref([])
    const isLoading = ref(false)
    const loadSalesProgressData = async (limit:number=10) => {
        try {
            isLoading.value = true
            const data = await getSalesProgressData()
            salesProgressData.value = data
            console.log(salesProgressData.value)
        } finally {
            isLoading.value = false
        }
    }

    onMounted(() => {
        loadSalesProgressData()
    })
    return {
        isLoading,
        salesProgressData,
        loadSalesProgressData   
    }
}