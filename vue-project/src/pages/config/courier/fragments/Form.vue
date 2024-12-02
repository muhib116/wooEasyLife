<template>
    <div 
        v-if="form" 
        class="grid gap-4 max-w-[500px] mx-auto py-10"
    >
        <Input.Primary
            label="API Key *"
            :placeholder="`Enter your ${activeTab} api key.`"
            v-model="form.api_key"
            @input="hasUnsavedData = true"
        />
        <Input.Primary
            label="Secret Key *"
            :placeholder="`Enter your ${activeTab} secret key.`"
            v-model="form.secret_key"
            @input="hasUnsavedData = true"
        />

        <Button.Primary 
            class="ml-auto"
            :class="hasUnsavedData ? '' : 'pointer-events-none opacity-60'"
            @onClick="(btn) => handleSaveCourierConfig(form, btn)"
        >
            Save
        </Button.Primary>
    </div>
</template>

<script setup lang="ts">
    import { Button, Input } from '@components'
    import { inject } from 'vue'

    const {
        handleSaveCourierConfig,
        hasUnsavedData,
        activeTab,
        form,
    } = inject("useCourier")
</script>