<template>
    <Base
        v-model="localValue"
        wrapperClass="block"
        labelClass="mb-1 text-[14px]"
        inputClass="placeholder:text-inherit placeholder:opacity-60 border border-secondary-five rounded-lg px-4 py-2 block w-full disabled:bg-gray-100 disabled:opacity-60 flex items-center justify-between"
        iconClass=""
        :iconSource="iconSource"
        :iconName="iconName"
        :label="label"
        @iconClick="$emit('onIconClick')"
    />
</template>

<script setup>
    import Base from '../Base.vue'
    defineOptions({
        name: 'PrimaryInput'
    })
    const props = defineProps({
        iconSource: String,
        iconName: String,
        label: String,
    })
    const localValue  = defineModel();
</script>