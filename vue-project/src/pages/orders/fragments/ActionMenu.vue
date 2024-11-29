<template>
    <Heading
        class="px-6 mb-6"
        title="Quick Actions"
    />
    <div class="[&>*]:border-t">
        <MenuButton
            title="Check Customer"
            subtitle="Check delivery success rate for selected items."
            iconName="PhUserList"
            :cb="handleFraudCheck"
        />
        <MenuButton
            title="Courier"
            subtitle="Book courier for selected items."
            iconName="PhTruck"
        />
    </div>
</template>

<script setup lang="ts">
    import { Heading } from '@components'
    import MenuButton from './fragments/MenuButton.vue'
    import { inject } from 'vue'
    

    const {
        handleFraudCheck
    } = inject('useOrders')
</script>