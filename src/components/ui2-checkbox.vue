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
    <span class="wa-checkbox">
      <input :checked="modelValue === trueValue" :name="name" :value="trueValue" type="checkbox"
             @change="emit('update:modelValue', ($event.target as HTMLInputElement).checked ? trueValue : falseValue)">
      <span>
        <span class="icon">
          <i class="fas fa-check"></i>
        </span>
      </span>
    </span>
    <slot />
  </label>
</template>
