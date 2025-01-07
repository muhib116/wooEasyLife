<template>
    <Layout>
        <Container>
            <div class="grid gap-8 font-light">
                <Filter />

                Sale Performance chart
                Order sources with chart
                Available Courier balance
                Courier Delevery Report
                Sales target
                Recent Order | Top Saling Product
                <div class="grid grid-cols-2 gap-5 w-full">
                    <RecentOrder />
                    <TopSellingProduct />
                </div>
                <FilteredData />
            </div>
        </Container>
    </Layout>
</template>
<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import FilteredData from './fragments/FilteredData.vue'
    import RecentOrder from './fragments/recentOrder/RecentOrder.vue'
    import TopSellingProduct from './fragments/topSallingProduct/TopSellingProduct.vue'
    import Filter from './fragments/Filter.vue'
    import { useDashboard } from './useDashboard'
    import { provide } from 'vue'

    const _useDashboard = useDashboard()

    provide('useDashboard', _useDashboard)
</script>