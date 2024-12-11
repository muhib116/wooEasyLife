import { onMounted, ref } from "vue"
import { startOfDay, endOfDay, subDays, startOfWeek, endOfWeek, startOfMonth, endOfMonth, startOfYear, endOfYear, format } from 'date-fns'
import { getOrderStatistics, getOrderStatuses } from "@/api"


export const useDashboard = () => {
    const filterOptions = [
        {
            id: 'today',
            title: 'Today'
        },
        {
            id: 'yesterday',
            title: 'Yesterday'
        },
        {
            id: 'this-week',
            title: 'This week'
        },
        {
            id: 'this-month',
            title: 'This month'
        },
        {
            id: 'this-year',
            title: 'This year'
        },
        {
            id: 'custom',
            title: 'Custom'
        },
    ]

    const selectedFilterOption = ref<string>('today')
    const orderStatistics = ref({})
    const isLoading = ref(false)
    const customDates = ref({
        start_date: '',
        end_date: ''
    })
    const orderStatuses = ref([])

    const loadOrderStatistics = async (start_date: string, end_date: string) => {
        try {
            isLoading.value = true
            const { data } = await getOrderStatistics({start_date, end_date})
            orderStatistics.value = data
        } finally {
            isLoading.value = false
        }
    }

    const loadOrderStatuses = async () => {
        const { data } = await getOrderStatuses()
        orderStatuses.value = data.map(item => {
            return {
                title: item.title,
                slug: item.slug
            }
        })
    }


    const getDateRangeFormatted = (period) => {
        let startDate, endDate
    
        switch (period) {
            case 'today':
                startDate = startOfDay(new Date()) // Start of the current day
                endDate = endOfDay(new Date()) // End of the current day
                break
    
            case 'yesterday':
                const yesterday = subDays(new Date(), 1) // Subtract one day from today
                startDate = startOfDay(yesterday) // Start of yesterday
                endDate = endOfDay(yesterday) // End of yesterday
                break
    
            case 'this-week':
                startDate= subDays(new Date(), 6)
                endDate = startOfDay(new Date())
                break
    
            case 'this-month':
                startDate = startOfMonth(new Date()) // Start of the current month
                endDate = endOfMonth(new Date()) // End of the current month
                break
    
            case 'this-year':
                startDate = startOfYear(new Date()) // Start of the current year
                endDate = endOfYear(new Date()) // End of the current year
                break
        }
    
        // Format dates as 'YYYY-MM-DD'
        return {
            startDate: startDate ? format(startDate, 'yyyy-MM-dd') : '',
            endDate: endDate ? format(endDate, 'yyyy-MM-dd') : '',
        }
    }

    const getDataByCustomFilter = async (button = {isLoading: false}) => {
        const { start_date, end_date } = customDates.value
        try {
            button.isLoading = true
            await loadOrderStatistics(start_date, end_date)
        } finally {
            button.isLoading = false
        }
    }

    const getData = async () => {
        if(selectedFilterOption.value == 'custom') return
        const { startDate, endDate } = getDateRangeFormatted(selectedFilterOption.value)
        await loadOrderStatistics(startDate, endDate)
    }

    onMounted(async () => {
        try {
            isLoading.value = true
            await getData()
            await loadOrderStatuses()
        } finally {
            isLoading.value = false
        }
    })

    return {
        selectedFilterOption,
        orderStatistics,
        orderStatuses,
        filterOptions,
        customDates,
        getDataByCustomFilter,
        getData,
    }
}