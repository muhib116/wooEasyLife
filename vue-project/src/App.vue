
<template>
  <RouterView />
</template>

<script setup lang="ts">
  import { onMounted } from 'vue'
  import { RouterView } from 'vue-router'

  onMounted(() => {
    // wp admin left side off
    document.body.classList.add('folded')
  })
</script>