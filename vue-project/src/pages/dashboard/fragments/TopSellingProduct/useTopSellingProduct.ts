import { getTopSellingProduct } from "@/api/dashboard"
import { onMounted, ref } from "vue"


export const useTopSellingProduct = () => {
    const topSellingProducts = ref([])
    const isLoading = ref(false)
    const loadTopSellingProduct = async (limit:number=10) => {
        try {
            isLoading.value = true
            const { data } = await getTopSellingProduct(limit)
            topSellingProducts.value = data
        } finally {
            isLoading.value = false
        }
    }

    onMounted(() => {
        loadTopSellingProduct()
    })
    return {
        isLoading,
        topSellingProducts,
        loadTopSellingProduct   
    }
}