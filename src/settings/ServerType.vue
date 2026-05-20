<script setup lang="ts">
import { inject } from 'vue'
import WaRadio from '../components/wa-radio.vue'
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
  <wa-radio :options="options" :ns="ns" name="Сервер API" :model-value="modelValue"
            @update:modelValue="$emit('update:modelValue', $event as string)" />
</template>
