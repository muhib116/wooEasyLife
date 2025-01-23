<template>
    <MessageBox
        v-if="!licenseKey"
        title="Don't have a license key? <br /> We're here to help! Contact us to get your key."
        type="info"
    />
    <MessageBox
        v-else-if="!isValidLicenseKey"
        :title="`
            <strong style='font-size: 18px;'>Your License Key Has Expired</strong>
            <br />
            <p>
                It looks like your license key is no longer active. Don’t worry—we’re here to help! Contact us to renew your key and regain access, or check out our renewal options to get started right away.
            </p>
        `"
        type="danger"
    />
</template>

<script setup lang="ts">
    import {
        MessageBox
    } from '@components'

    defineProps<{
        isValidLicenseKey: boolean
        licenseKey: string
    }>()
</script>