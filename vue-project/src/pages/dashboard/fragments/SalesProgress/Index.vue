<template>
    <DashboardCard
        title="Sales Progress"
        :Key="chartKey"
        @dateChange="loadSalesProgressData"
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
    import { useSalesProgress } from './useSalesProgress.ts'
    import DashboardCard from '../DashboardCard.vue'

    const {
        chartKey,
        isLoading,
        salesProgressData,
        loadSalesProgressData 
    } = useSalesProgress()

    const chartData = computed(() => {
        return {
            type: 'bar',
            options: {
                xaxis: {
                    categories: salesProgressData.value?.categories || []
                },
                colors: ['#f97315']
            },
            series: salesProgressData.value?.series || []
        }
    })
</script>