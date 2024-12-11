<template>
    <h3 class="text-xl font-semibold text-gray-900 mb-3">
        Configure your license
        {{ licenseKey }}
    </h3>
    <Card.Native class="relative min-h-[200px]">
        <Loader
            :active="isLoading"
            class="absolute inset-x-1/2 -translate-x-1/2"
        />
        <div class="flex gap-6 items-center mb-6">
            <span class="font-bold">Enter License Key</span>
            <Input.Primary
                wrapperClass="max-w-[400px] w-full"
                v-model="licenseKey"
            />
        </div>
        <p>If you don't have License key, please contact with us to get your license key.</p>
        <div class="text-orange-500 mt-4 space-y-1">
            <div class="flex items-center gap-3">
                <Icon
                    name="PhPhone"
                    size="30"
                />
                Phone: 01789-909958 <br/>
            </div>

            <div class="flex items-center gap-3">
                <Icon
                    name="PhWhatsappLogo"
                    size="30"
                />
                Whatsapp: 01789-909958
            </div>
        </div>
    </Card.Native>
</template>

<script setup lang="ts">
    import { Card, Input, Icon, Loader } from '@components'
    import { useLicense } from './UseLicense'

    const {
        isLoading,
        licenseKey
    } = useLicense()
</script>