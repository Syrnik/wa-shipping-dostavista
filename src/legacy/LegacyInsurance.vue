<script setup lang="ts">
import LegacyWaField from './LegacyWaField.vue'
import { inject, ref } from 'vue'
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
  <legacy-wa-field name="Сумма страховки" name-class="for-checkbox">
    <div v-for="(o, idx) in options" :key="o.value" :class="idx === 0 ? 'value no-shift' : 'value'">
      <label>
        <input type="radio" :name="addns('type', ns)" :value="o.value" v-model="value.type"
               @change="$emit('update:modelValue', value)">
        {{ o.title }}
      </label>
    </div>
    <div class="value" v-show="value.type === 'custom'">
      <input type="text" class="long" :name="addns('value', ns)" v-model.trim="value.value"
             @input="$emit('update:modelValue', value)" placeholder="0">
    </div>
  </legacy-wa-field>
</template>
