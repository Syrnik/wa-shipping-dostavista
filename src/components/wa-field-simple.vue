<script setup lang="ts">
import WaField from './wa-field.vue'

defineProps<{
  name?: string
  valueClass?: string
}>()
</script>

<template>
  <wa-field :name="name">
    <div class="value" :class="valueClass">
      <slot />
    </div>
  </wa-field>
</template>
