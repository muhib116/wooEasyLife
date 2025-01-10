import { ref } from "vue";

export type ChartData = {
    type: 'line' | 'bar' | 'pie' | 'donut' | 'radar' | 'area' | 'scatter'
    series: Array<{ name: string; data: number[] }>
    categories?: string[] // Optional for X-axis labels
    options?: Record<string, any> // Optional for additional configuration
}

export const useChart = () => 
{
    const defaultChartData = ref({
        type: 'area',
        options: {
            chart: {
                toolbar: {
                    show: false, // Hides the toolbar
                },
            },
            xaxis: {
                // categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            },
        },
        series: [
            // {
            //     name: 'Sales',
            //     data: [30, 40, 35, 50, 49, 60, 70],
            // },
            // {
            //     name: 'Sales',
            //     data: [10, 70, 35, 10, 39, 60, 20],
            // },
        ],
    });
    
    
    
    return {
        defaultChartData
    }
}