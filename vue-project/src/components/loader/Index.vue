<template>
    <div
        v-if="active"
        class="flex gap-1 items-end"
        :style="{
            width: `${size || 32}px`,
            height: `${size || 32}px`,
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