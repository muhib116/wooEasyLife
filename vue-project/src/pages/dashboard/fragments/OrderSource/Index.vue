<template>
    {{ chartData }}
    <DashboardCard
        title="Order Source"
        :Key="chartKey"
        @dateChange="loadOrderSourceData"
    >
        <Loader
            :active="isLoading"
        />
        <div class="-ml-3 -mr-3 h-[320px]">
            <Chart.Native
                :chartData="chartData"
                width="100%"
                height="100%"
            />
        </div>
    </DashboardCard>
</template>

<script setup lang="ts">
    import {
        Chart,
        Loader
    } from '@components'
    import { computed } from 'vue'
    import { useOrderSource } from './useOrderSource.js'
    import DashboardCard from '../DashboardCard.vue'

    const {
        chartKey,
        isLoading,
        orderSourceData,
        loadOrderSourceData 
    } = useOrderSource()

    const chartData = computed(() => {
        return {
            options: {
                xaxis: {
                    categories: orderSourceData.value?.categories || []
                },
                colors: ['#39c1a0']
            },
            series: [
                {
                    name: 'Total Order',
                    type: 'bar',
                    data: orderSourceData.value?.series?.length ? orderSourceData.value?.series[0].data : []
                }
                
            ]
        }
    })
</script>