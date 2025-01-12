<template>
    <DashboardCard
        title="Sales summary"
        @dateChange="loadSalesSummaryData"
    >
        <Loader
            :active="isLoading"
            class="bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
        />
        <div class="-ml-3 -mr-3 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            <Card.Stylist
                :title="salesSummaryData?.total_orders || 0"
                subtitle="Total orders"
                iconName="PhPackage"
                iconWeight="duotone"
            />
            <Card.Stylist
                :title="salesSummaryData?.average_order_value || 0"
                subtitle="Average order value"
                iconName="PhMoney"
                iconWeight="duotone"
            />
            <Card.Stylist
                :title="salesSummaryData?.total_sale_amount || 0"
                subtitle="Total sale amount"
                iconName="PhCashRegister"
                iconWeight="duotone"
            />
            <Card.Stylist
                :title="salesSummaryData?.total_discount_amount || 0"
                subtitle="Total discount amount"
                iconName="PhSealPercent"
                iconWeight="duotone"
            />
            <Card.Stylist
                :title="salesSummaryData?.total_shipping_cost || 0"
                subtitle="Total shipping"
                iconName="PhTruck"
                iconWeight="duotone"
            />
        </div>
    </DashboardCard>
</template>

<script setup lang="ts">
    import {
        Card,
        Loader
    } from '@components'
    import { computed } from 'vue'
    import { useSalesSummary } from './useSalesSummary'
    import DashboardCard from '../DashboardCard.vue'

    const {
        isLoading,
        salesSummaryData,
        loadSalesSummaryData  
    } = useSalesSummary()

    const chartData = computed(() => {
        return {
            type: 'line',
            options: {
                xaxis: {
                    categories: orderProgressData.value?.categories || []
                }
            },
            series: orderProgressData.value?.series || []
        }
    })
</script>