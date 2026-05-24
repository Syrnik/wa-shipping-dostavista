<script setup lang="ts">
defineProps<{ name?: string; nameClass?: string }>()
</script>

<template>
  <div class="field">
    <div class="name" :class="nameClass" v-if="name">{{ name }}</div>
    <slot />
  </div>
</template>
