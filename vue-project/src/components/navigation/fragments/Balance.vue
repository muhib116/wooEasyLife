<template>
    <Button.Native
        v-if="isValidLicenseKey"
        :loading="userDataLoading"
        class="py-[6px] px-4 rounded font-bold text-white mr-4"
        :class="{
            'animate-bounce' : balance <= 5
        }"
        :style="{
            backgroundColor: getBgColor
        }"
    >
        Balance: {{ userData?.remaining_order || 0 }}
    </Button.Native>
</template>

<script setup lang="ts">
    import { Button } from '@/components'
    import { watch, inject, ref } from 'vue'

    const {
        userData,
        userDataLoading,
        isValidLicenseKey
    } = inject('useServiceProvider')

    const balance = ref(userData.value?.remaining_order || 0);
    const getBgColor = ref('#00b002');

    watch(() => [userDataLoading], () => {
        if (balance.value <= 20 && balance.value > 10) {
            getBgColor.value = '#f97315';
        } else if (balance.value <= 10 && balance.value > 5) {
            getBgColor.value = '#ff4733';
        } else if (balance.value <= 5) {
            getBgColor.value = '#ff0000';
        }
    }, {
        deep: true,
        immediate: true
    })
</script>