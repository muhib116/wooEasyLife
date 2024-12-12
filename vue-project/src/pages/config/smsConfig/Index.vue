<template>
    <Card.Native class="py-10">
        <Heading
            class="mb-3"
            title="Configure your order place Message"
            subtitle="To add new line in message add \n. <span class='text-blue-500'>Ex: Name \n Address</span>"
        />

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
        <Button.Primary class="mt-4 ml-auto">
            Save Changes
        </Button.Primary>
    </Card.Native>
</template>

<script setup lang="ts">
    import { Card, Input, Button, Heading } from '@components'
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