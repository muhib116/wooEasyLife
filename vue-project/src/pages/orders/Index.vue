<template>
    <Layout>
        <Container>
            <div class="flex gap-6 w-full items-start">
                <Card.Native class="flex-1 !px-0">
                    <OrderList />
                </Card.Native>
                <ActionMenu />
            </div>
        </Container>
    </Layout>
</template>
<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import { Card } from '@components'
    import { useOrders } from './useOrders'
    import { provide } from 'vue'
    import OrderList from './fragments/OrderList.vue'
    import ActionMenu from './fragments/ActionMenu.vue'

    const _useOrders = useOrders()

    provide('useOrders', _useOrders)
</script>