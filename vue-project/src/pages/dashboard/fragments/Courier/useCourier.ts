import { ref } from "vue"

export const useCourier = () => {
    const courierData = ref([])
    const isLoading = ref(false)

    const loadCourierData = async (date: {start_date: string, end_date: string}) => {
        try {
            isLoading.value = true
            const data = ''
            courierData.value = data
        } finally {
            isLoading.value = false
        }
    }

    return {
        isLoading,
        courierData,
        loadCourierData 
    }
}