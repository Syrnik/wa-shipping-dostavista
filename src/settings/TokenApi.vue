<script setup lang="ts">
import WaField from '../components/wa-field.vue'
import { inject, ref } from 'vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{ modelValue?: string }>(), { modelValue: '' })
const emit = defineEmits<{ 'update:modelValue': [value: string] }>()

const value = ref(props.modelValue)
const ns = addns('token', inject<string>('namespace', ''))
</script>

<template>
  <wa-field name="Токен API" name-class="for-input">
    <input v-model.trim="value" :name="ns" class="long" type="text" @input="$emit('update:modelValue', value)">
  </wa-field>
</template>
