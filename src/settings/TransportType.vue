<script setup lang="ts">
import WaRadio from '../components/wa-radio.vue'
import { inject, ref } from 'vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = defineProps<{ modelValue: number }>()
const emit = defineEmits<{ 'update:modelValue': [value: number] }>()

const options = [
  { value: 0, title: 'Не передавать' },
  { value: 1, title: 'Легковой автомобиль / джип / пикап (до 500 кг)' },
  { value: 2, title: 'Каблук (до 700 кг)' },
  { value: 3, title: 'Микроавтобус / портер (до 1000 кг)' },
  { value: 4, title: 'Газель (до 1500 кг)' },
  { value: 5, title: 'Грузовой автомобиль' },
  { value: 6, title: 'Пеший курьер' },
  { value: 7, title: 'Легковой автомобиль' },
]

const setting = ref(props.modelValue)
const ns = addns('transport_type', inject<string>('namespace', ''))
</script>

<template>
  <wa-radio name="Тип транспорта"
            :options="options"
            v-model.number="setting"
            :ns="ns"
            @update:modelValue="emit('update:modelValue', setting)">
    <span class="hint">Тип транспорта, которым должен быть доставлен заказ. Если не передавать никакого, то сервер
      Dostavista попробует определить тип транспорта исходя из параметров заказа.</span>
  </wa-radio>
</template>
