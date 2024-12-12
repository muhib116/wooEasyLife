<template>
    <Card.Native class="py-10">
        <h3 class="text-xl font-semibold text-gray-900 mb-3">
            Configure your order place Message
        </h3>

        <div class="grid gap-4">
            <Input.Primary
                label="Admin Phone"
                placeholder="Enter admin phone number"
                v-model="form.admin_phone"
            />
            
            <TextInputArea
                label="Customer Message"
                placeholder="Write customer message"
                :dropdownData="personalizations"
                v-model="form.customer_message"
            />
            
            <TextInputArea
                label="Admin Message"
                placeholder="Write admin message"
                :dropdownData="personalizations"
                v-model="form.admin_message"
                position="up"
            />
        </div>
        Sms config
    </Card.Native>
</template>

<script setup lang="ts">
    import { Card, Input, Textarea } from '@components'
    import { useSmsConfig } from './useSmsConfig'
    import { provide, ref } from 'vue'
    import TextInputArea from './fragments/TextInputArea.vue'

    const _useSmsConfig = useSmsConfig()
    const {
        form,
        personalizations
    } = _useSmsConfig

    provide('useSmsConfig', _useSmsConfig)
</script>