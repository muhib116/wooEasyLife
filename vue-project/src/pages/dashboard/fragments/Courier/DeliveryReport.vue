<template>
    <DashboardCard
        title="Delivery Report"
        @dateChange="loadCourierDeliveryData"
    >
        <Loader
            :active="isLoading"
            class="bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
        />
        <div class="h-[320px]">
            {{ courierDeliveryData }}
        </div>
    </DashboardCard>
</template>

<script setup lang="ts">
    import {
        Chart,
        Loader
    } from '@components'
    import DashboardCard from '../DashboardCard.vue'
    import { useDeliveryReport } from './useDeliveryReport'

    const {
        isLoading,
        courierDeliveryData,
        loadCourierDeliveryData 
    } = useDeliveryReport()
</script>