<script setup lang="ts">
import { inject } from 'vue'
import MinMaxLimit from '../components/min-max-limit.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{
  modelValue?: { min: number | null; max: number | null }
}>(), {
  modelValue: () => ({ min: null, max: null }),
})

const emit = defineEmits<{ 'update:modelValue': [value: { min: number | null; max: number | null }] }>()

const ns = addns('weight_limits', inject<string>('namespace', ''))
</script>

<template>
  <min-max-limit :model-value="modelValue"
                 :ns="ns"
                 ui="2.0"
                 name="Ограничение по весу"
                 step="0.01"
                 @update:modelValue="emit('update:modelValue', $event)">
    кг. <br><span class="hint">Укажите минимальный и/или максимальный вес заказа</span>
  </min-max-limit>
</template>
