import { ref } from "vue"
import { 
    startOfDay, 
    endOfDay, 
    subDays, 
    startOfMonth, 
    endOfMonth, 
    startOfYear, 
    endOfYear, 
    format, 
} from 'date-fns'


export const useDashboard = (mountable?: boolean) => {
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

    const selectedFilterOption = ref<string>('this-week')
    const orderStatistics = ref({})
    const customDates = ref({
        start_date: '',
        end_date: ''
    })

    const getDateRangeFormatted = (period: string) => {
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
        customDates.value = {
            start_date: startDate ? format(startDate, 'yyyy-MM-dd') : '',
            end_date: endDate ? format(endDate, 'yyyy-MM-dd') : ''
        }
        return {
            startDate: customDates.value.start_date,
            endDate: customDates.value.end_date,
        }
    }

    return {
        selectedFilterOption,
        orderStatistics,
        filterOptions,
        customDates,
        getDateRangeFormatted
    }
}