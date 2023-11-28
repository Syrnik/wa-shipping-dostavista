<script setup>
const props = defineProps({modelValue: {}, options: {type: Array, default: () => []}, waClass: {}, name: String, disabled: {type: Boolean, default: false}})
defineEmits(['update:modelValue']);
</script>

<template>
  <div class="wa-select">
    <select :class="waClass" :name="name" @change="$emit('update:modelValue', $event.target.value)" :disabled="disabled">
      <option v-for="o in options" :selected="o.value === modelValue" :value="o.value">{{ o.title }}</option>
    </select>
  </div>
</template>
