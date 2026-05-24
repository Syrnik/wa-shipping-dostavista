<script setup lang="ts">
import WaField from '../components/wa-field.vue'
import { inject, ref } from 'vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{ modelValue?: string }>(), { modelValue: '' })
defineEmits<{ 'update:modelValue': [value: string] }>()

const address = ref(props.modelValue)
const ns = addns('name', addns('location_from', inject<string>('namespace', '')))
</script>

<template>
  <wa-field name="Адрес отправки" name-class="for-input">
    <input type="text" class="long" v-model.trim="address" :name="ns" :class="{ 'state-error': !address.length }">
    <p class="hint">Укажите адрес, от которого отправляется курьер. Город, улица и дом.</p>
  </wa-field>
</template>
