<template>
    <div
        v-if="active"
        class="flex gap-1 items-end z-50 bg-white/90 rounded-full p-[2px] absolute inset-1/2 -translate-x-1/2"
        :style="{
            width: `${size || 40}px`,
            height: `${size || 40}px`,
        }"
    >
        <Icon
            name="PhSpinner"
            :size="size"
            class="animate-spin !w-full !h-full"
        />
    </div>
</template>

<script setup>
    import { Icon } from '@components'

    defineOptions({
        name: 'Loader',
    })
    const props = defineProps({
        active: {
            type: Boolean,
            default: () => true,
        },
        size: {
            type: String,
            default: '32',
        }
    })
</script>