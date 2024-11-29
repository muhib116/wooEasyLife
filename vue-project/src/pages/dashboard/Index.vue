<template>
    <Layout>
        <Container>
            <div class="grid gap-8">
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
</script>