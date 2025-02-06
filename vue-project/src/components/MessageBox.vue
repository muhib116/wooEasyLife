<template>

    <div 
        v-if="title"
        class="flex items-center shadow rounded text-sm p-4 mb-4 border-l-4 border-current gap-3 relative" role="alert"
        :style="styles[type.toLowerCase()]"
    >
        <button 
            class="absolute top-2 right-2 aspect-squire bg-white rounded p-[2px] hover:text-red-500 hover:scale-110 border border-gray-200 hover:border-[currentColor] pointer-events-auto"
            @click="$emit('onClose')"
        >
            <Icon
                name="PhX"
                size="18"
                weight="bold"
            />
        </button>
        <Icon
            v-if="!cleanBox"
            :name="icons[type.toLowerCase()]"
            size="24"
        />
        <div>
            <div v-if="!cleanBox">
                <span class="font-semibold"><span class="capitalize">{{ type }}</span> alert!</span> 
                <br />
            </div>
        <div v-html="title"></div>
        </div>
    </div>  
</template>

<script setup lang="ts">
    import { Icon } from '@components'

    defineProps<{
        type: "success" | "danger" | "warning" | "info"
        title: string,
        cleanBox: boolean
    }>()

    const styles = {
        success: {
            backgroundColor: '#e1f2e6',
            color: '#2fcc71'
        },
        danger: {
            backgroundColor: '#fde6e6',
            color: '#e23c3c'
        },
        warning: {
            backgroundColor: '#fef5e7',
            color: '#f0b433'
        },
        info: {
            backgroundColor: '#e3eef8',
            color: '#0084c7'
        },
    }

    const icons = {
        success: 'PhChecks',
        danger: 'PhRadioactive',
        warning: 'PhWarning',
        info: 'PhQuestion',
    }
</script>