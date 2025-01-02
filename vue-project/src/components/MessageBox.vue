<template>

<div 
    v-if="title"
    class="flex items-center text-sm p-4 mb-4 border-l-4 border-current gap-3" role="alert"
    :style="styles[type.toLowerCase()]"
>
    <Icon
        :name="icons[type.toLowerCase()]"
        size="24"
    />
    <div>
      <span class="font-semibold"><span class="capitalize">{{ type }}</span> alert!</span> 
      <br />
      <div v-html="title"></div>
    </div>
  </div>  
</template>

<script setup lang="ts">
    import { Icon } from '@components'

    defineProps<{
        type: "success" | "danger" | "warning" | "info"
        title: string
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