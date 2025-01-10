<template>
    <DashboardCard
        title="Sales Progress"
    >
        <Chart.Native
            :chartData="chartData"
            width="100%"
        />
    </DashboardCard>
</template>

<script setup lang="ts">
    import {
        Chart,
        Card
    } from '@components'
    import { ref } from 'vue'
    import { useSalesProgress } from './useSalesProgress.ts'
    import DashboardCard from '../DashboardCard.vue'

    const {
        isLoading,
        salesProgressData,
        loadSalesProgressData 
    } = useSalesProgress()

    const chartData = ref({
        type: 'bar'
    });
</script>