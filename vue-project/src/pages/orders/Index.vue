<template>
    <Layout>
        <Container class="py-10">
            <div class="flex gap-6 w-full">
                <Card.Native class="flex-1 !px-0">
                    <OrderList />
                </Card.Native>
                <Card.Native class="basis-1/4">
                    <Heading
                        title="Quick Actions"
                    />
                </Card.Native>
            </div>
        </Container>
    </Layout>
</template>
<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import { Card, Heading } from '@components'
    import { useOrders } from './useOrders'
    import { provide } from 'vue'
    import OrderList from './fragments/OrderList.vue'

    const _useOrders = useOrders()

    provide('useOrders', _useOrders)
</script>