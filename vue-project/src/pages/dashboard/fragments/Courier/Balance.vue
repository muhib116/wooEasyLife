<template>
    <DashboardCard
        title="Courier Balance"
        :showDateFilter="false"
        @dateChange="loadCourierData"
    >
        <Loader
            :active="isLoading"
            class="bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
        />
        <div class="space-y-4">
            <Card.Stylist
                title="50000tk"
                subtitle="Total Balance"
                class="bg-[#693d84]"
            />

            <div class="grid grid-cols-2 gap-6">
                <Card.Stylist
                    title="500tk"
                    subtitle="Steadfast"
                />
                <Card.Stylist
                    title="350tk"
                    subtitle="Paperfly"
                    class="bg-[#017cef]"
                />
                <Card.Stylist
                    title="5345tk"
                    subtitle="Redx"
                    class="bg-[#eb2128]"
                />
                <Card.Stylist
                    title="534tk"
                    subtitle="Pathao"
                    class="bg-[#e83335]"
                />
            </div>
        </div>
    </DashboardCard>
</template>

<script setup lang="ts">
    import {
        Card,
        Table,
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