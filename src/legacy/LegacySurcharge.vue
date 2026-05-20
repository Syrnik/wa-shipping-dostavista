<script setup lang="ts">
import WaField from '../components/wa-field.vue'
import { inject } from 'vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{ modelValue?: string }>(), { modelValue: '' })
defineEmits<{ 'update:modelValue': [value: string] }>()

const ns = addns('surcharge', inject<string>('namespace', ''))
</script>

<template>
  <wa-field name="Корректировка стоимости доставки" name-class="for-input">
    <input type="text" class="long" :value="modelValue"
           @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)" :name="ns"
           placeholder="S">
    <p class="hint">Фиксированная стоимость или формула для наценки/скидки на расчётную стоимость доставки.
      Можно оставить пустым, тогда будет использоваться стоимость, посчитанная через API.
      Доступные переменные: S — стоимость доставки, Z — стоимость заказа с учётом скидок,
      Y — стоимость заказа без учёта скидок.</p>
  </wa-field>
</template>
