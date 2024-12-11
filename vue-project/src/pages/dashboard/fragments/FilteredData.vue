<template>
    <Card.Native>
        <div class="mb-4">
            <h1 class="font-semibold text-xl">Filtered Data</h1>
            <p class="opacity-60 text-[18px]">
                Here is the filtered data, now you are watching {{ selectedFilterOption.replace('-', ' ') }}'s data
            </p>
        </div>

        <div class="grid grid-cols-4 gap-6 !text-white relative">
            <Loader
                :active="true"
                class="absolute inset-x-1/2 top-[200px] -translate-x-1/2 z-20"
            />
            <Card.Stylist
                v-for="(item, index) in orderStatuses"
                :key="index"
                class="bg-blue-500 text-white order-status"
                :class="`status-${item.slug}`"
                :title="orderStatistics.status_wise[item.slug] || 0"
                :subtitle="item.title"
                :iconName="iconsWithBg[item.slug]?.icon"
            />
            
            <Card.Stylist
                class="bg-cyan-600 text-white"
                :title="orderStatistics?.total_revenue"
                subtitle="total revenue"
                iconName="PhCoins"
            />
        </div>
    </Card.Native>
</template>
<script setup lang="ts">
    import { Card, Loader } from '@components'
    import { inject } from 'vue'

    const {
        orderStatuses,
        orderStatistics,
        isLoading,
        selectedFilterOption
    } = inject('useDashboard')

    const iconsWithBg = {
        processing: {
            icon: 'PhBasket'
        },
        "follow-up": {
            icon: 'PhHeadset'
        },
        "confirmed": {
            icon: 'PhCheck'
        },
        "call-not-received": {
            icon: 'PhPhoneX'
        },
        "fake": {
            icon: 'PhXCircle'
        },
        "courier-entry": {
            icon: 'PhListNumbers'
        },
        "courier-hand-over": {
            icon: 'PhPackage'
        },
        "out-for-delivery": {
            icon: 'PhTruck'
        },
        "delivered": {
            icon: 'PhHandDeposit'
        },
        "payment-received": {
            icon: 'PhCreditCard'
        },
        "pending-payment": {
            icon: 'PhCardholder'
        },
        "returned": {
            icon: 'PhArrowUUpLeft'
        },
        "refunded": {
            icon: 'PhClockCounterClockwise'
        },
        "on-hold": {
            icon: 'PhHandGrabbing'
        },
        "completed": {
            icon: 'PhFlag'
        },
        "cancelled": {
            icon: 'PhX'
        },
        "checkout-draft": {
            icon: 'PhFileDashed'
        },
    }
</script>