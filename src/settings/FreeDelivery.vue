<script setup lang="ts">
import { ref } from 'vue'
import WaField from '../components/wa-field.vue'

const props = withDefaults(defineProps<{ modelValue?: number | null }>(), { modelValue: null })
defineEmits<{ 'update:modelValue': [value: number | null] }>()

const free_delivery = ref(props.modelValue)
</script>

<template>
  <wa-field name="Порог бесплатной доставки" name-class="for-input">
    <input class="shortest text-right" type="number" min="0" step="0.01" v-model.number="free_delivery"
           @input="$emit('update:modelValue', free_delivery)" placeholder="Нет">
    <p class="hint">Если сумма заказа больше либо равна указанной, то доставка в ПВЗ будет бесплатной.
      Оставьте поле пустым, если доставка всегда платная. Поставьте 0, если доставка всегда бесплатная.</p>
  </wa-field>
</template>
