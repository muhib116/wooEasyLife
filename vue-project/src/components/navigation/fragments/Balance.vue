<template>
    <button 
        class="px-2 rounded font-bold text-white mr-4"
        :class="{
            'animate-bounce' : balance <= 5
        }"
        :style="{
            backgroundColor: getBgColor
        }"
    >
        Balance: {{ userData?.remaining_order || 0 }}
    </button>
</template>

<script setup lang="ts">
    import { computed, inject, ref } from 'vue'

    const {
        userData
    } = inject('useServiceProvider')

    const balance = ref(userData.value?.remaining_order || 0);
    const getBgColor = computed(() => {
        let color = '#00b002';

        if (balance.value <= 20 && balance.value > 10) {
            color = '#f97315';
        } else if (balance.value <= 10 && balance.value > 5) {
            color = '#ff4733';
        } else if (balance.value <= 5) {
            color = '#ff0000';
        }

        return color;
    });
</script>