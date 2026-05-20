<script setup lang="ts">
import { inject, ref, watch } from 'vue'
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
  <wa-field name="Время доставки" name-class="for-checkbox">
    <ul>
      <li v-for="o in options" :key="o.value"><label>
        <span class="wa-radio">
          <input type="radio" v-model="delivery_time" :name="ns" :value="o.value">
          <span></span>
        </span>
        {{ o.title }}
      </label></li>
    </ul>
    <div v-show="delivery_time === 'exact_delivery_time'">
      <input type="number" class="shortest numerical" v-model.number="exact_hours"
             :name="addns('exact_delivery_time', namespace)"
             @input="$emit('update:exactHours', exact_hours!)">
      <p class="hint">Среднее время в часах, которое требуется курьеру для доставки заказа.
        Оно прибавляется к общему времени готовности заказа с учетом значений в таблице
        «Интервалы доставки» с дополнительными выходными и рабочими днями.</p>
    </div>
  </wa-field>
</template>
