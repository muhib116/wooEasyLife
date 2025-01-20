<template>
    <Layout>
        <Container>
            <Loader
                class="absolute inset-1/2 -translate-x-1/2 bg-white rounded-full shadow"
                :active="isLoading"
            />
            <Card.Native>
                <MessageBox
                    :title="alertMessage.title"
                    :type="alertMessage.type"
                />

                <div class="flex gap-4 mb-4">
                    <Button.Native
                        v-for="item in filter"
                        @onClick="btn => handleFilter(item, btn)"
                        class="font-light hover:text-orange-500"
                        :class="selectedFilter == item.slug ? 'text-orange-500' : ''"
                    >
                        {{ item.title }}
                    </Button.Native>
                </div>

                <OrderList />
            </Card.Native>
        </Container>
    </Layout>
</template>

<script setup lang="ts">
    import { Layout, Container } from '@layout'
    import { Button, MessageBox, Card, Loader } from '@/components'
    import { useMissingOrder } from './useMissingOrder'
    import OrderList from './fragments/OrderList.vue'
    import { provide } from 'vue'

    const _useMissingOrder = useMissingOrder()
    const {
        filter,
        isLoading,
        alertMessage,
        selectedFilter,
        handleFilter,
    } = _useMissingOrder

    provide('useMissingOrder', _useMissingOrder)
</script>