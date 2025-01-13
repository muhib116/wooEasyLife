<template>
    <div class="grid lg:grid-cols-2 gap-5 w-full">
        <DeliveryReport />
        <Balance />
    </div>
</template>

<script setup lang="ts">
    import DeliveryReport from './DeliveryReport.vue'
    import Balance from './Balance.vue'
</script>