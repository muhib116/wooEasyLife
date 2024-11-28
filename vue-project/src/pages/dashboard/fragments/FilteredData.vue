<template>
    <Card.Native>
        <div class="mb-4">
            <h1 class="font-semibold text-xl">Filtered Data</h1>
            <p class="opacity-60 text-[18px]">Here is the filtered data, now you are watching last 7 days data</p>
        </div>

        <div class="grid grid-cols-4 gap-6">
            <Card.Stylist
                class="bg-green-600"
                title="3,000"
                subtitle="Total Complete Order"
            >
                <template #footer>
                    <div class="text-center">
                        Progress Up <span class="text-3xl">🔥</span>
                    </div>
                </template>
            </Card.Stylist>

            <Card.Stylist
                class="!bg-orange-500"
                title="10"
                subtitle="Total New Order"
                iconName="PhPackage"
            >
                <template #footer>
                    <div class="text-center">
                        Congratulations <span class="text-3xl">🎉</span>
                    </div>
                </template>
            </Card.Stylist>
            
            <Card.Stylist
                class="!bg-yellow-500"
                title="120"
                subtitle="Total refunded Order"
                iconName="PhArrowUUpLeft"
            >
                <template #footer>
                    <div class="text-center">
                        Progress down <span class="text-3xl">⤴</span>
                    </div>
                </template>
            </Card.Stylist>
            
            <Card.Stylist
                class="!bg-red-700"
                title="120"
                subtitle="Total Fake Order"
                iconName="PhImageBroken"
            >
                <template #footer>
                    <div class="text-center">
                        Progress down <span class="text-3xl">🥸</span>
                    </div>
                </template>
            </Card.Stylist>

            <Card.Stylist
                class="!bg-red-500"
                title="120"
                subtitle="Total Cancel Order"
                iconName="PhReceiptX"
            >
                <template #footer>
                    <div class="text-center">
                        Progress down <span class="text-3xl">😩</span>
                    </div>
                </template>
            </Card.Stylist>
        </div>
    </Card.Native>
</template>
<script setup lang="ts">
    import { Card } from '@components'
</script>