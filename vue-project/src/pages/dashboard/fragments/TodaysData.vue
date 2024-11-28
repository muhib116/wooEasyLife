<template>
    <Card.Native>
        <div class="mb-4">
            <h1 class="font-semibold text-xl">Todays Data</h1>
            <p class="opacity-60 text-[18px]">Here is the overview of todays orders</p>
        </div>

        <div class="grid grid-cols-4 gap-6 !text-white">
            <Card.Stylist
                class="bg-blue-500 text-white"
                title="3,000"
                subtitle="Under Processing"
                iconName="PhGearSix"
            />
            
            <Card.Stylist
                class="bg-gray-500 text-white"
                title="3,000"
                subtitle="Total On Hold Orders"
                iconName="PhPause"
            />
            <Card.Stylist
                class="bg-sky-500 text-white"
                title="3,000"
                subtitle="total parcel In courier"
                iconName="PhTruck"
            />
            <Card.Stylist
                class="bg-cyan-600 text-white"
                title="3,000"
                subtitle="total parcel In courier"
                iconName="PhTruck"
            />
        </div>
    </Card.Native>
</template>
<script setup lang="ts">
    import { Card } from '@components'
</script>