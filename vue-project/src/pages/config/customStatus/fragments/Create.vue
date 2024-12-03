<template>
    <div class="max-w-[400px] mx-auto space-y-4">
        <Heading
            title="Create new status"
            class="mb-2"
        />

        <Input.Primary
            label="Title"
            placeholder="Enter title"
        />

        <ColorPicker v-model="color" />
        
        <Input.Primary
            label="Description"
        />
    </div>
</template>

<script setup lang="ts">
    import { Input, ColorPicker, Heading } from '@components'
    import { inject, ref } from 'vue'

    const color = ref('#000000')

    const {
        
    } = inject('useCustomStatus')
</script>