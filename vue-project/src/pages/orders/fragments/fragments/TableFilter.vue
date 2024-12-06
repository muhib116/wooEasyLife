<template>
    <div class="flex items-center [&>*+*]:border-l [&>*+*]:border-l-gray-400 text-sm px-4 mb-2 text-extralight text-sky-600">
        <Button.Native
            class="capitalize text-sm px-2"
            :class="orderFilter.status == '' ? 'font-semibold' : 'font-light'"
            @click="btn => handleFilter('', btn)"
        >
            All
        </Button.Native>
        <template
            v-for="(item, index) in orderStatusWithCounts"
            :key="index"
        >
            <Button.Native
                v-if="item"
                class="capitalize text-sm px-2 gap-1" 
                :class="orderFilter.status == item.slug ? 'font-semibold' : 'font-light'"
                @click="btn => handleFilter(item.slug, btn)"
            >
                {{ item.title }}
                <span class="text-gray-700">({{ item.count }})</span>
            </Button.Native>
        </template>
    </div>
</template>

<script setup lang="ts">
    import { inject } from 'vue'
    import { Button } from '@components'

    const {
        getOrders,
        orderFilter,
        orderStatusWithCounts
    } = inject('useOrders')

    const handleFilter = async (status: string, btn) => {
        try {
            btn.isLoading = true
            orderFilter.value.status = status
            await getOrders()
        } finally {
            btn.isLoading = false
        }
    }
</script>