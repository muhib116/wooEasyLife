<template>
    <div>
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
                        @change="getData"
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
    import { Heading, Card } from '@components'
    import { inject } from 'vue'

    defineProps<{
        title?: string,
        subtitle?: string
    }>()


    const {
        filterOptions,
        selectedFilterOption,
        getData,
        customDates,
        getDataByCustomFilter
    } = inject('useDashboard')
</script>