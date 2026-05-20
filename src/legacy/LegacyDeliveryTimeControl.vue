<script setup lang="ts">
import { inject, ref, watch } from 'vue'
import LegacyWaField from './LegacyWaField.vue'
import WaField from '../components/wa-field.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{
  modelValue?: string
  exactHours?: number
}>(), {
  modelValue: '',
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'update:exactHours': [value: number]
}>()

const namespace = inject<string>('namespace', '')
const ns = addns('delivery_time', namespace)

const options = [
  { value: '', title: 'Не определено' },
  { value: '+3 hour', title: 'В течение дня' },
  { value: '+1 day', title: 'Следующий день' },
  { value: '+1 day, +2 days', title: '1—2 дня' },
  { value: '+2 days, +3 days', title: '2—3 дня' },
  { value: '+1 week', title: '1 неделя' },
  { value: 'exact_delivery_time', title: 'Указанное количество часов' },
]

const delivery_time = ref(props.modelValue)
const exact_hours = ref(props.exactHours)

watch(delivery_time, v => emit('update:modelValue', v))
</script>

<template>
  <legacy-wa-field name="Время доставки" name-class="for-checkbox">
    <div v-for="(o, idx) in options" :key="o.value" :class="idx === 0 ? 'value no-shift' : 'value'">
      <label><input type="radio" v-model="delivery_time" :name="ns" :value="o.value"> {{ o.title }}</label>
    </div>
    <wa-field v-show="delivery_time === 'exact_delivery_time'">
      <input type="number" class="short numerical" v-model.number="exact_hours"
             :name="addns('exact_delivery_time', namespace)"
             @input="$emit('update:exactHours', exact_hours!)">
      <p class="hint">Среднее время в часах, которое требуется курьеру для доставки заказа.</p>
    </wa-field>
  </legacy-wa-field>
</template>
