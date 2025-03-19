<script setup>
import WaField from './wa-field.vue'

const props = defineProps(['name', 'valueClass']);
</script>

<template>
  <wa-field :name="name">
    <div class="value" :class="valueClass">
      <slot/>
    </div>
  </wa-field>
</template>

