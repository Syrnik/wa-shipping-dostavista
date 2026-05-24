<script setup lang="ts">
const props = withDefaults(defineProps<{
  name?: string
  falseValue?: string
  trueValue?: string
  modelValue?: unknown
}>(), {
  name: '',
  falseValue: '0',
  trueValue: '1',
})

const emit = defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <input :name="name" :value="falseValue" type="hidden">
  <label>
    <input type="checkbox"
           :name="name"
           :value="trueValue"
           :checked="modelValue === trueValue"
           @change="emit('update:modelValue', ($event.target as HTMLInputElement).checked ? trueValue : falseValue)">
    <slot />
  </label>
</template>
