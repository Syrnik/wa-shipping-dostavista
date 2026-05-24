<template>
  <wa-field>
    от <input v-model.number="setting.min" :class="ui === '2.0' ? 'shortest' : 'short'" :name="addns('min', ns)"
              :step="step" class="numerical" min="0"
              placeholder="0"
              type="number"
              @input="$emit('update:modelValue', setting)">
    до <input v-model.number="setting.max" :class="ui === '2.0' ? 'shortest' : 'short'" :name="addns('max', ns)"
              :step="step"
              class="numerical" min="0"
              placeholder="∞"
              type="number"
              @input="$emit('update:modelValue', setting)">
    <slot />
  </wa-field>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import WaField from './wa-field.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{
  modelValue: { min: number | null; max: number | null }
  ns: string
  step?: number | string
  ui?: string
}>(), {
  ui: '2.0',
})

defineEmits<{ 'update:modelValue': [value: { min: number | null; max: number | null }] }>()

const setting = ref(props.modelValue)
</script>
