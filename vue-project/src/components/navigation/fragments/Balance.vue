<template>
    <button class="px-2 rounded bg-green-500 text-white mr-4">
        Balance: {{ userData.remaining_order }}
    </button>
</template>

<script setup lang="ts">
    import { inject } from 'vue'

    const {
        userData
    } = inject('useServiceProvider')
</script>