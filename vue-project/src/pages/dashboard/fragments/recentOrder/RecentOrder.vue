<template>
    <Card.Native>
        Recent Order
    </Card.Native>
</template>

<script setup lang="ts">
    import { Card } from '@components'
    import { useRecentOrder } from './UseRecentOrder'

    const {

    } = useRecentOrder()
</script>