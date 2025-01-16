import { getSalesTarget } from "@/api/dashboard"
import { computed, onMounted, ref } from "vue"

export const useSalesTarget = () => {
    const defaultData = {
        monthly_target_amount: null,
        start_date: ''
    }
    const form = ref({...defaultData})
    const isLoading = ref(false)
    const salesTargetData = ref({})

    const loadSalesTargetData = async () => {
        try {
            isLoading.value = true
            const { data } = await getSalesTarget(form)
            salesTargetData.value = data
        } finally {
            isLoading.value = false
        }
    }

    const dailySalesAmount = computed(() => {
        return (form.value.monthly_target_amount || 0) / 30
    })

    onMounted(() => {
        loadSalesTargetData()
    })

    return {
        form,
        isLoading,
        salesTargetData,
        dailySalesAmount,
        loadSalesTargetData
    }
}