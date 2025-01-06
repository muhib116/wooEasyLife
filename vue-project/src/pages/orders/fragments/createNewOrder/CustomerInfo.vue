<template>
    <Input.Primary
        type="date"
        label="Date"
    />
    <div class="grid grid-cols-2 gap-4">
        <Input.Primary
            label="Customer Name *"
            placeholder="Write customer name."
        />
        <Input.Primary
            label="Customer Phone Number *"
            placeholder="Write valid phone number."
        />
    </div>

    <Textarea.Native
        label="Customer Address *"
        placeholder="Write customer address."
    />

    <div class="flex gap-4">
        <Select.Primary
            label="Order Source *"
            :options="orderSource"
            v-model="selectedSource"
            wrapperClass="flex-1"
        />
        <Input.Primary
            v-if="selectedSource == 'other'"
            label="Order source name"
            placeholder="Write order source name."
            wrapperClass="flex-1"
        />
    </div>
</template>

<script setup lang="ts">
    import { Input, Select, Textarea } from '@components'
    import { ref } from 'vue'

    const selectedSource = ref(null)
    const form = ref({
        created_via: ''
    })
    const orderSource = [
        {
            id: 'whats-app',
            title: 'Whats app'
        },
        {
            id: 'messenger',
            title: 'Messenger'
        },
        {
            id: 'instagram',
            title: 'Instagram'
        },
        {
            id: 'phone-call',
            title: 'Phone call'
        },
        {
            id: 'other',
            title: 'Other'
        },
    ]
</script>