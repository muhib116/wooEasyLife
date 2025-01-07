import { getTopSellingProduct } from "@/api/dashboard"
import { onMounted, ref } from "vue"


export const useTopSellingProduct = () => {
    const topSellingProducts = ref([])
    const loadTopSellingProduct = async (limit:number=10) => {
        const { data } = await getTopSellingProduct(limit)
        topSellingProducts.value = data
    }

    onMounted(() => {
        loadTopSellingProduct()
    })
    return {
        topSellingProducts,
        loadTopSellingProduct   
    }
}