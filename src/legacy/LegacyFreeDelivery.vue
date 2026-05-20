<script setup lang="ts">
import WaField from '../components/wa-field.vue'
import { inject, ref } from 'vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{ modelValue?: number | null }>(), { modelValue: null })
defineEmits<{ 'update:modelValue': [value: number | null] }>()

const free_delivery = ref(props.modelValue)
const ns = addns('free_delivery', inject<string>('namespace', ''))
</script>

<template>
  <wa-field name="Порог бесплатной доставки" name-class="for-input">
    <input class="short" type="number" min="0" step="0.01" v-model.number="free_delivery"
           :name="ns" @input="$emit('update:modelValue', free_delivery)" placeholder="Нет">
    <p class="hint">Если сумма заказа больше либо равна указанной, то доставка в ПВЗ будет бесплатной.
      Оставьте поле пустым, если доставка всегда платная. Поставьте 0, если доставка всегда бесплатная.</p>
  </wa-field>
</template>
