<script setup lang="ts">
import WaRadio from '../components/wa-radio.vue'
import { inject, ref } from 'vue'
import FormulaDrawer from './FormulaDrawer.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

interface InsuranceSetting {
  type: string
  value: string
}

const props = withDefaults(defineProps<{
  modelValue?: InsuranceSetting
}>(), {
  modelValue: () => ({ type: 'none', value: '' }),
})

const emit = defineEmits<{ 'update:modelValue': [value: InsuranceSetting] }>()

const ns = addns('insurance', inject<string>('namespace', ''))
const value = ref({ ...props.modelValue })

const options = [
  { value: 'none', title: 'Без страховки' },
  { value: 'raw', title: 'Стоимость товара без скидок' },
  { value: 'total', title: 'Стоимость товара со скидками' },
  { value: 'custom', title: 'Фиксированная или формула' },
]
</script>

<template>
  <wa-radio name="Сумма страховки" :options="options" v-model="value.type"
            @update:model-value="$emit('update:modelValue', value)" :ns="addns('type', ns)">
    <template v-if="value.type === 'custom'">
      <input type="text" class="long" :name="addns('value', ns)" v-model.trim="value.value"
             @input="$emit('update:modelValue', value)" placeholder="0">
      <formula-drawer :vars="['z', 'y']"><template #header>Формула расчёта страховой стоимости</template></formula-drawer>
    </template>
  </wa-radio>
</template>
