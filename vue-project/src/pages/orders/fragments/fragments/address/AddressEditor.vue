<template>
    <div class="relative">
        <div class="flex bg-white z-20  justify-between items-center sticky -top-3">
            <h3 class="font-semibold text-lg mb-2">
                {{ title }}
            </h3>

            <Button.Primary
                v-if="isEditable"
                :class="isEditable ? 'animate-bounce' : 'opacity-60'"
                @onClick="handleUpdate"
                icon="PhCheck"
                title="Click to save"
            >
                Save
            </Button.Primary>
            <Button.Native
                v-else
                class="!p-0 bg-transparent shadow-none opacity-60"
                @onClick="handleUpdate"
                title="Click to edit"
            >
                <Icon
                    name="PhNotePencil"
                    size="25"
                />
            </Button.Native>
        </div>

        <AddressForm
            v-if="isEditable"
            :address="address"
            :billingAddress="billingAddress"
        />
        <AddressPreview
            v-else
            :address="address"
        />
    </div>
</template>

<script setup lang="ts">
    import { inject, ref } from 'vue'
    import AddressPreview from './AddressPreview.vue'
    import AddressForm from './AddressForm.vue'
    import { Icon, Button } from '@components'

    const props = defineProps<{
        title?: string
        address: object
        billingAddress: object
    }>()

    const {
        handleAddressEdit
    } = inject('useAddress')
    const isEditable = ref(false)

    const handleUpdate = async (btn) => {
        if(isEditable.value){
            try {
                btn.isLoading = true
                const data = await handleAddressEdit(props.address)
                console.log(data)
            } finally {
                btn.isLoading = false
            }
        }
        isEditable.value = !isEditable.value
    }
</script>