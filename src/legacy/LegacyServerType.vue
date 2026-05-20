<script setup lang="ts">
import { inject } from 'vue'
import LegacyWaRadio from './LegacyWaRadio.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

defineProps<{ modelValue?: string }>()
defineEmits<{ 'update:modelValue': [value: string] }>()

const ns = addns('api_server', inject<string>('namespace', ''))
const options = [
  { value: 'test', title: 'Тестовый' },
  { value: 'production', title: 'Рабочий' },
]
</script>

<template>
  <legacy-wa-radio :options="options" :ns="ns" name="Сервер API" :model-value="modelValue"
                   @update:modelValue="$emit('update:modelValue', $event as string)" />
</template>
