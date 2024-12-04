<template>
    <div class="max-w-[450px] mx-auto space-y-4 relative">
        <MessageBox
            class="absolute top-0 z-50 w-full"
            :title="alertMessage?.message"
            :type="alertMessage?.type"
        />
        <Heading
            v-if="!hideTitle"
            title="Create new status"
            class="mb-2"
        />

        <Input.Primary
            label="Title *"
            placeholder="Enter title"
            v-model="form.label"
        />

        <div class="px-1">
            <ColorPicker 
                label="Select Color *"
                v-model="form.color"
            />
        </div>
        
        <Input.Primary
            label="Description *"
            v-model="form.description"
        />

        <slot name="btn">
            <Button.Primary
                @onClick="handleCustomStatusCreate"
                class="ml-auto"
            >
                Create Status
            </Button.Primary>
        </slot>
    </div>
</template>

<script setup lang="ts">
    import { Input, ColorPicker, Heading, Button, MessageBox } from '@components'
    import { inject } from 'vue'

    const {
        form,
        alertMessage,
        handleCustomStatusCreate
    } = inject('useCustomStatus')

    defineProps<{
        hideTitle: boolean
    }>()
</script>