<template>
    <Layout>
        <Container>
            <div class="grid gap-8 font-light">
                <Filter />
                <TodaysData />
                <FilteredData />
            </div>
        </Container>
    </Layout>
</template>
<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import FilteredData from './fragments/FilteredData.vue'
    import TodaysData from './fragments/TodaysData.vue'
    import Filter from './fragments/Filter.vue'
    import { useDashboard } from './useDashboard'
    import { provide } from 'vue'

    const _useDashboard = useDashboard()

    provide('useDashboard', _useDashboard)
</script>