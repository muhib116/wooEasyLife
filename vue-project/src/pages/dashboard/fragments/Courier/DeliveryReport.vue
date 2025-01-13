<template>
    <DashboardCard
        title="Delivery Report"
        @dateChange="loadCourierData"
    >
        <Loader
            :active="isLoading"
            class="bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
        />
        <div class="h-[320px]">
            Delivery Report
        </div>
    </DashboardCard>
</template>

<script setup lang="ts">
    import {
        Chart,
        Loader
    } from '@components'
    import DashboardCard from '../DashboardCard.vue'
    import { useCourier } from './UseCourier'

    const {
        isLoading,
        courierData,
        loadCourierData
    } = useCourier()
</script>