import { createOrUpdateWPOption, getWPOption } from "@/api"
import { computed, onMounted, ref } from "vue"
import { add, format, parseISO } from 'date-fns'

export const useSalesTarget = () => 
{
    const alertMessage = ref({
        title: '',
        type: 'info'
    })

    const defaultData = {
        monthly_target_amount: null,
        daily_target_amount: null,
        start_date: new Date().toISOString().split('T')[0], // Default to today's date in 'yyyy-MM-dd' format
        end_date: ''
    }

    const isLoading = ref(false)

    const salesTargetData = ref({
        option_name: 'woo_easy_life_sales_target',
        data: {
            ...defaultData
        }
    })

    const dailyTargetAmount = computed(() => {
        return (salesTargetData.value.data.monthly_target_amount || 0) / 30
    })

    const endDate = computed(() => {
        let startDate = salesTargetData.value.data?.start_date
        console.log({ startDate })

        if (typeof startDate === 'string' && startDate) {
            startDate = parseISO(startDate) // Convert ISO string to Date object
        }

        if (!startDate || isNaN(new Date(startDate).getTime())) {
            console.error('Invalid start_date value:', salesTargetData.value.data.start_date)
            return '' // Return an empty string or handle the error appropriately
        }

        const date = add(new Date(startDate), { days: 30 }) // Add 30 days
        return format(date, 'yyyy-MM-dd') // Format as 'yyyy-MM-dd'
    })

    const loadSalesTargetData = async () => {
        try {
            isLoading.value = true
            const { data } = await getWPOption({
                option_name: 'woo_easy_life_sales_target'
            }) // Replace with your actual API call

            salesTargetData.value = {
                ...salesTargetData.value,
                data: data
            }
        } finally {
            isLoading.value = false
            setTimeout(() => {
                alertMessage.value.title = ''
            }, 6000)
        }
    }

    const saveSalesTarget = async (btn) => {
        try {
            btn.isLoading = true
            isLoading.value = true
            const { data } = await createOrUpdateWPOption(salesTargetData.value)

            alertMessage.value = {
                title: 'Sales target saved!',
                type: 'success'
            }
        } finally {
            btn.isLoading = false
            isLoading.value = false
        }
    }

    onMounted(() => {
        loadSalesTargetData()
    })

    return {
        endDate,
        isLoading,
        alertMessage,
        salesTargetData,
        dailyTargetAmount,
        saveSalesTarget,
        loadSalesTargetData,
    }
}