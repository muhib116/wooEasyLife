<template>
    <Layout>
        <Container>
            <div class="grid gap-4 md:gap-6 lg:gap-8 font-light">
                <OrderStatistics />

                <div class="grid lg:grid-cols-2 gap-5 w-full">
                    <OrderProgress />
                    <SalesProgress />
                </div>
                
                <div class="grid lg:grid-cols-2 gap-5 w-full">
                    <OrderSource />
                    <OrderCycleTime />
                </div>
                

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 w-full">
                    <RecentOrder />
                    <TopSellingProduct />
                </div>
            </div>
        </Container>
    </Layout>
</template>

<script setup lang="ts">
    import { provide } from 'vue'
    import { Layout, Container } from '@layout'
    import OrderStatistics from './fragments/OrderStatistics/Index.vue'
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