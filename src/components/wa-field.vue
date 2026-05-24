<script setup lang="ts">
defineProps<{
  htmlName?: string
  name?: string
  nameClass?: string
}>()
</script>

<template>
  <div class="field">
    <div class="name" :class="nameClass" v-if="!!htmlName" v-html="htmlName"></div>
    <div class="name" :class="nameClass" v-else>{{ name }}</div>
    <div class="value"><slot /></div>
  </div>
</template>
