<template>
    <div class="relative">
        <Card.Native>
            <div class="flex justify-between">
                <Heading
                    :title="title"
                    :subtitle="subtitle"
                />
                <label class="font-light border px-3 py-2 rounded">
                    <select 
                        class="outline-none bg-transparent w-full !border-none focus:outline-none"
                        v-model="selectedFilterOption"
                        @change="handleLoadData($emit)"
                    >
                        <option
                            v-for="(option, index) in filterOptions"
                            :key="index"
                            :value="option.id"
                        >
                            {{ option.title }}
                        </option>
                    </select>
                </label>
            </div>
            <slot></slot>
        </Card.Native>
    </div>

    <Modal 
        v-model="toggleModal" 
        title="Enter your date range."
        class="w-[600px]"
    >
        <div class="w-full grid gap-3">
            <div class="grid">
                Start date
                <label class="font-light border px-3 py-2 rounded">
                    <input
                        class="outline-none bg-transparent w-full !border-none focus:outline-none"
                        type="date"
                        v-model="customDates.start_date"
                    />
                </label>
            </div>

            <div class="grid">
                End date
                <label class="font-light border px-3 py-2 rounded">
                    <input
                        class="outline-none bg-transparent w-full !border-none focus:outline-none"
                        type="date"
                        v-model="customDates.end_date"
                    />
                </label>
            </div>

            <Button.Primary
                class="ml-auto"
                @click="() => {
                    $emit('dateChange', customDates)
                    toggleModal = false
                }"
            >
                Apply Now
            </Button.Primary>
        </div>
    </Modal>
</template>

<script setup lang="ts">
    import { Heading, Card, Modal, Button } from '@components'
    import { useDashboard } from '../useDashboard'
    import { onMounted, ref } from 'vue'

    defineProps<{
        title?: string,
        subtitle?: string
    }>()

    const emit = defineEmits(['dateChange'])

    const toggleModal = ref(false)
    const {
        filterOptions,
        selectedFilterOption,
        customDates,
        getDateRangeFormatted
    } = useDashboard()

    const handleLoadData = () => {
        if(selectedFilterOption.value == 'custom'){
            toggleModal.value = true
            return
        }
        getDateRangeFormatted(selectedFilterOption.value)
        emit('dateChange', customDates.value)
    }

    onMounted(() => {
        emit('dateChange', customDates.value)
    })
</script>