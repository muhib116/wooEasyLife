<template>
    <DashboardCard
        title="Sales Target"
        :showDateFilter="false"
        @dateChange="() => {}"
    >
        <template #before-filter>
            <Button.Outline
                class="border-orange-500 bg-orange-50 text-orange-500"
                title="Make a sales target"
                @onClick="toggleModal = true"
            >
                <Icon
                    name="PhPlus"
                    weight="bold"
                />
            </Button.Outline>
        </template>

        <Loader
            class="bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
        />
        <div class="grid grid-cols-3 gap-4">
            <Card />
            <Card />
            <Card />
        </div>
    </DashboardCard>

    <Modal 
        v-model="toggleModal"
        @close="toggleModal = false"
        title="Set your sales target"
        class="max-w-[550px] w-full"
        hideFooter
    >
        <SalesTargetForm />
    </Modal>
</template>

<script setup lang="ts">
    import {
        Loader, 
        Button, 
        Icon,
        Modal
    } from '@components'
    import DashboardCard from '../DashboardCard.vue'
    import { ref } from 'vue'
    import SalesTargetForm from './SalesTargetForm.vue'
    import Card from './Card.vue'

    const toggleModal = ref(true)
    // import { useCourier } from './UseCourier'

    // const {
    //     isLoading,
    //     courierData,
    //     loadCourierData
    // } = useCourier()
</script>