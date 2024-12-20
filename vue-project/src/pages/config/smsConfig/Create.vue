<template>
    <MessageBox
        :title="alertMessage.message"
        :type="alertMessage.type"
    />
    <Loader
        class="absolute inset-1/2 z-20"
        :active="isLoading"
    />
    <Heading
        class="mb-3"
        title="Create message"
    />

    <div class="grid gap-4">
        {{ form }}
        <Select.Primary
            label="Select Status"
            :options="wooStatuses"
            itemKey="slug"
            v-model="form.status"
        />
        <Select.Primary
            label="Select Receiver"
            :options="messageFor"
            itemKey="slug"
            v-model="form.message_for"
        />
        <Input.Primary
            v-if="form.message_for == 'admin'"
            label="Admin Phone Number"
            placeholder="Enter admin phone number"
            v-model="form.phone_number"
        />
        <TextInputArea
            label="Message"
            placeholder="Write message"
            :dropdownData="personalizations"
            v-model="form.message"
            position="up"
        />

        <div class="flex gap-4 items-center">
            Active
            <Switch v-model="form.is_active"/>
        </div>
    </div>
    <Button.Primary
        class="mt-4 ml-auto"
        @onClick="btn => handleCreateSMS(btn, form)"
    >
        Save Changes
    </Button.Primary>
</template>
<script setup lang="ts">
    import { Select, Button, Heading, Loader, MessageBox, Switch, Input } from '@components'
    import { inject, onMounted } from 'vue'
    import TextInputArea from './fragments/TextInputArea.vue'

    const {
        form,
        wooStatuses,
        personalizations,
        handleCreateSMS,
        isLoading,
        alertMessage,
        loadWooStatuses,
        messageFor
    } = inject('useSmsConfig')

    onMounted(async () => {
        await loadWooStatuses()
    })
</script>