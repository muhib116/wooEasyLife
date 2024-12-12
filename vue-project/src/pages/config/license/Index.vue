<template>
    <MessageBox
        :title="alertMessage.message"
        :type="alertMessage.type"
    />
    <h3 class="text-xl font-semibold text-gray-900 mb-3">
        Configure your license
    </h3>
    <Card.Native class="relative min-h-[200px] !py-10">
        <Loader
            :active="isLoading"
            class="absolute inset-x-1/2 -translate-x-1/2 z-20"
        />
        <div class="flex gap-6 items-center mb-6">
            <span class="font-bold">Enter License Key</span>
            <Input.Native
                class="border px-3 py-2 rounded-sm max-w-[300px] w-full bg-transparent"
                v-model="licenseKey"
            />
            <Button.Primary 
                v-if="!isLoading"
                class="!bg-green-500"
                @onClick="ActivateLicense"
            >
                Activate License
            </Button.Primary>
        </div>

        <p>If you don't have License key, please contact with us to get your license key.</p>
        <div class="mt-4 space-y-1 flex gap-6">
            <div class="flex items-center gap-3">
                <Icon
                    class="bg-blue-500 text-white p-1"
                    name="PhPhone"
                    size="30"
                />
                01789-909958
            </div>

            <div class="flex items-center gap-3">
                <Icon
                    class="bg-green-500 text-white p-1"
                    name="PhWhatsappLogo"
                    size="30"
                />
                01789-909958
            </div>
        </div>
        <!-- v-if="licenseKey && !isLoading" -->
        <Button.Primary 
            class="!bg-red-500 mt-6"
            @onClick="deactivateLicense"
        >
            Deactivate License
        </Button.Primary>
    </Card.Native>
</template>

<script setup lang="ts">
    import { Card, Input, Icon, Loader, Button, MessageBox } from '@components'
    import { useLicense } from './UseLicense'

    const {
        isLoading,
        licenseKey,
        deactivateLicense,
        ActivateLicense,
        alertMessage
    } = useLicense()
</script>