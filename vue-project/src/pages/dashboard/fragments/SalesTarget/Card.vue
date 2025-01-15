<template>
    <div
        class="border shadow-md p-6 rounded-sm"
    >
        <Heading
            title="Daily"
            titleClass="!text-base"
        />
        <hr class="my-2" />
        <div class="grid grid-cols-[55%_1fr] items-center pt-4 gap-4">
            <div class="grid gap-4 text-black">
                <div class="bg-green-100 border-l-4 border-green-500 px-3 py-1">
                    <h3>Achievement</h3>
                    <h4 class="font-semibold">400tk</h4>
                </div>
                <div class="bg-[#f3e4fc] border-l-4 border-[#693d84] px-3 py-1">
                    <h3>Target</h3>
                    <h4 class="font-semibold">400tk</h4>
                </div>
            </div>
    
            <div>
                <Chart.Native
                    width="150px"
                    :chartData="chartData"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang='ts'>
    import { Heading, Chart } from '@components'
    import { computed } from 'vue'

    const chartData = computed(() => {
        return {
            type: 'pie',
            options: {
                xaxis: {
                    categories: ['Achieved', 'Target']
                },
                colors: ['#02b795', '#693d84'],
                legend: {
                    show: false
                }
            },
            series: [300, 600]
        }
    })
</script>