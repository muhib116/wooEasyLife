<template>
    <div class="relative">
        <Loader
            :active="isLoading"
            class="bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
        />
        <div class="space-y-4 pr-1">
            {{ form }}
            <Input.Primary
                label="Monthly sales target amount"
                type="number"
                v-model="form.monthly_target_amount"
            />
            <Input.Primary
                label="Daily sales target amount"
                type="number"
                :bgStyle="{background: '#efefef'}"
                :modelValue="dailySalesAmount"
            />
            <Input.Primary
                label="Start Date"
                type="date"
                v-model="form.start_date"
            />
            <Input.Primary
                label="End Date"
                type="date"
                disabled
                :bgStyle="{background: '#efefef'}"
            />
            <Button.Primary 
                class="ml-auto"
                @onClick="loadSalesTargetData"
            >
                Save
            </Button.Primary>
        </div>
    </div>
</template>

<script setup lang="ts">
    import { Input, Button } from '@components'
    import { useSalesTarget } from './useSalesTarget'
    import { provide } from 'vue'

    const _useSalesTarget = useSalesTarget()
    const {
        form,
        isLoading,
        salesTargetData,
        dailySalesAmount,
        loadSalesTargetData
    } = _useSalesTarget

    provide('useSalesTarget', _useSalesTarget)
</script>