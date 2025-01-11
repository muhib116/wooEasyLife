<template>
    <div class="relative">
        <Card.Native>
            <div v-if="showCustomDateInput" class="w-full text-sm flex items-end mb-2 gap-3">
                <div class="flex-1 grid grid-cols-2 gap-3">
                    <div class="grid">
                        Start date
                        <label class="font-light border px-3 py-1 rounded-sm">
                            <input
                                class="outline-none bg-transparent w-full !border-none focus:outline-none"
                                type="date"
                                v-model="customDates.start_date"
                            />
                        </label>
                    </div>
        
                    <div class="grid">
                        End date
                        <label class="font-light border px-3 py-1 rounded-sm">
                            <input
                                class="outline-none bg-transparent w-full !border-none focus:outline-none"
                                type="date"
                                v-model="customDates.end_date"
                            />
                        </label>
                    </div>
                </div>
    
                <Button.Primary
                    class="ml-auto w-[118px] text-center justify-center !py-[6px]"
                    @click="() => {
                        $emit('dateChange', customDates)
                    }"
                >
                    Apply Now
                </Button.Primary>
            </div>

            <div class="flex justify-between items-end mb-4">
                <Heading
                    :title="title"
                    :subtitle="subtitle"
                />

                <label class="font-light border px-2 py-1 rounded-sm">
                    <select 
                        class="outline-none bg-transparent w-full !border-none focus:outline-none"
                        v-model="selectedFilterOption"
                        @change="handleLoadData()"
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
</template>

<script setup lang="ts">
    import { Heading, Card, Button } from '@components'
    import { useDashboard } from '../useDashboard'
    import { onMounted, ref } from 'vue'

    defineProps<{
        title?: string,
        subtitle?: string
    }>()

    const emit = defineEmits(['dateChange'])

    const showCustomDateInput = ref(false)
    const {
        filterOptions,
        selectedFilterOption,
        customDates,
        getDateRangeFormatted
    } = useDashboard()

    const handleLoadData = () => {
        if(selectedFilterOption.value == 'custom'){
            showCustomDateInput.value = true
            return
        }

        showCustomDateInput.value = false
        getDateRangeFormatted(selectedFilterOption.value)
        emit('dateChange', customDates.value)
    }

    onMounted(() => {
        getDateRangeFormatted(selectedFilterOption.value)
        emit('dateChange', customDates.value)
    })
</script>