<template>
    <slot></slot>
</template>

<script setup lang="ts">
    import { onMounted, provide, watch } from 'vue'
    import { isEmpty } from 'lodash'
    import {
        useServiceProvider
    } from './useServiceProvider'
    import { useRouter, useRoute } from 'vue-router'

    const _useServiceProvider = useServiceProvider()
    const { 
        loadUserData, 
        userData,
        router,
        route
    } = _useServiceProvider

    const getNoticeOfBalanceOver = (balance: number) => {
        let balanceNotice = {
            type: '',
            message: ''
        }

        if (balance <= 0) {
            balanceNotice = {
                type: 'danger',
                message: `
                    <h1 class="font-medium text-lg text-red-600">⚠ Your Balance is Depleted!</h1>
                    <p class="mt-2">You can no longer process new orders because your balance has run out.</p>
                    <p class="mt-2 font-semibold">Recharge now to continue enjoying seamless order processing with WooEasyLife.</p>
                    <p class="mt-2">Don’t miss out on new orders—stay ahead by keeping your balance topped up!</p>
                `
            }
        } else if (balance > 0 && balance <= 5) {
            balanceNotice = {
                type: 'warning',
                message: `
                    <h1 class="font-medium text-lg text-yellow-600">⚠ Low Balance Alert!</h1>
                    <p class="mt-2">You're running low on balance! Only <strong>${balance}</strong> left.</p>
                    <p class="mt-2">Once your balance is exhausted, you won't be able to process new orders.</p>
                    <p class="mt-2 font-semibold">Recharge now to avoid interruptions and keep your orders flowing!</p>
                `
            }
        } else if (balance > 5 && balance <= 25) {
            balanceNotice = {
                type: 'info',
                message: `
                    <h1 class="font-medium text-lg text-blue-600">🔔 Balance Running Low!</h1>
                    <p class="mt-2">You still have <strong>${balance}</strong> in your account, but it's getting low.</p>
                    <p class="mt-2">Consider recharging soon to ensure uninterrupted order processing.</p>
                    <p class="mt-2 font-semibold">Stay ahead and top up before your balance runs out!</p>
                `
            }
        }

        return balanceNotice;
    }

    router.value = useRouter()
    route.value = useRoute()

    onMounted(async () => {
        if(isEmpty(userData.value)) {
            await loadUserData()
            userData.value.notice = getNoticeOfBalanceOver(userData.value.remaining_order || 0)
        }
    })

    provide('useServiceProvider', _useServiceProvider)
</script>