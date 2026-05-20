<script setup lang="ts">
interface Option {
  value: string
  title: string
}

const props = withDefaults(defineProps<{
  modelValue?: unknown
  options?: Option[]
  waClass?: string
  name?: string
  disabled?: boolean
}>(), {
  options: () => [],
  disabled: false,
})

defineEmits<{ 'update:modelValue': [value: string] }>()
</script>

<template>
  <div class="wa-select">
    <select :class="waClass" :name="name" @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)" :disabled="disabled">
      <option v-for="o in options" :key="o.value" :selected="o.value === modelValue" :value="o.value">{{ o.title }}</option>
    </select>
  </div>
</template>
