<template>
    <div>
        <RouterLink
            v-for="(item, index) in routes"
            :key="index"
            :to="item.to"
            class="cursor-pointer group flex gap-3 items-center px-3.5 py-4 text-sm hover:text-orange-600 hover:bg-orange-50 font-medium rounded-md text-gray-600"
            exact-active-class="text-orange-600 bg-orange-50"
        >
            <Icon
                :name="item.iconName"
                size="25"
            />
            {{ item.title }}
        </RouterLink>
    </div>
</template>

<script setup lang="ts">
    import { Icon } from '@components'

    const routes = [
        {
            to: {
                name: 'license'
            },
            title: 'License',
            iconName: 'PhKey'
        },
        {
            to: {
                name: 'smsConfig'
            },
            title: 'SMS Config',
            iconName: 'PhGearFine'
        },
        {
            to: {
                name: 'sendSms'
            },
            title: 'Send SMS',
            iconName: 'PhEnvelope'
        },
        {
            to: {
                name: "integration"
            },
            title: 'Integration',
            iconName: 'PhArrowsCounterClockwise'
        },
        {
            to: {
                name: "courier"
            },
            title: 'Courier',
            iconName: 'PhPackage'
        },
        {
            to: {
                name: "recharge"
            },
            title: 'Recharge',
            iconName: 'PhCreditCard'
        },
    ]
</script>