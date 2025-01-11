<template>
    <Layout>
        <Container>
            <div class="grid gap-8 font-light">
                <Filter />

                <div class="grid grid-cols-2 gap-5 w-full">
                    <OrderProgress />
                    <SalesProgress />
                </div>
                
                <div class="grid grid-cols-2 gap-5 w-full">
                    <OrderSource />
                    <OrderCycleTime />
                </div>
                

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
    import { provide } from 'vue'
    import { Layout, Container } from '@layout'
    import FilteredData from './fragments/FilteredData.vue'
    import RecentOrder from './fragments/recentOrder/RecentOrder.vue'
    import TopSellingProduct from './fragments/topSallingProduct/TopSellingProduct.vue'
    import Filter from './fragments/Filter.vue'
    import { useDashboard } from './useDashboard'
    import SalesProgress from './fragments/SalesProgress/Index.vue'
    import OrderProgress from './fragments/OrderProgress/Index.vue'
    import OrderSource from './fragments/OrderSource/Index.vue'
    import OrderCycleTime from './fragments/OrderCycleTime/Index.vue'

    const _useDashboard = useDashboard(true)

    provide('useDashboard', _useDashboard)
</script>