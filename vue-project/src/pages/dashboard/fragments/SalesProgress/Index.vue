<template>
    {{ chartData }}
    <DashboardCard
        title="Sales Progress"
        @dateChange="loadSalesProgressData"
    >
        <Loader
            :active="isLoading"
        />
        <Chart.Native
            :Key="chartKey"
            :chartData="chartData"
            width="100%"
        />
    </DashboardCard>
</template>

<script setup lang="ts">
    import {
        Chart,
        Loader
    } from '@components'
    import { computed, ref } from 'vue'
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
                }
            },
            series: salesProgressData.value?.series || []
        }
    });
</script>