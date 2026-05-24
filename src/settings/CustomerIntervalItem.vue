<script setup lang="ts">
import { ref } from 'vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

interface IntervalRow {
  from: string
  from_m: string
  to: string
  to_m: string
  day: number[]
  workday: boolean
  holiday: boolean
}

const props = withDefaults(defineProps<{
  modelValue?: IntervalRow
  ns?: string
}>(), {
  modelValue: () => ({ from: '10', from_m: '00', to: '12', to_m: '00', day: [1, 2, 3, 4, 5], workday: false, holiday: false }),
  ns: '',
})

const emit = defineEmits<{
  'update:modelValue': [value: IntervalRow]
  'delete': []
}>()

const interval = ref({ ...props.modelValue })

function emitChanges() {
  emit('update:modelValue', JSON.parse(JSON.stringify(interval.value)))
}
</script>

<template>
  <tr>
    <td class="nowrap">с
      <input type="text" class="shortest w-shipping-dostavista__hour-minute-input align-right" :name="addns('from', ns)"
             v-model.trim="interval.from" @input="emitChanges"> :
      <input type="text" class="shortest align-right w-shipping-dostavista__hour-minute-input" :name="addns('from_m', ns)"
             v-model.trim="interval.from_m" @input="emitChanges">
    </td>
    <td class="nowrap">до
      <input type="text" class="shortest w-shipping-dostavista__hour-minute-input align-right" :name="addns('to', ns)"
             v-model.trim="interval.to" @input="emitChanges"> :
      <input type="text" class="shortest align-right w-shipping-dostavista__hour-minute-input" :name="addns('to_m', ns)"
             v-model.trim="interval.to_m" @input="emitChanges">
    </td>
    <td v-for="v in [1, 2, 3, 4, 5, 6, 7]" :key="v">
      <input type="checkbox" :name="addns('', addns('day', ns))" :value="v"
             v-model="interval.day" @change="emitChanges">
    </td>
    <td><input type="checkbox" :name="addns('holiday', ns)" v-model="interval.holiday" @change="emitChanges"></td>
    <td><input type="checkbox" :name="addns('workday', ns)" v-model="interval.workday" @change="emitChanges"></td>
    <td><a class="inline-link" @click.prevent="$emit('delete')"><i class="icon16 delete fa fa-trash-alt text-red"></i></a></td>
  </tr>
</template>

<style lang="stylus">
input.w-shipping-dostavista__hour-minute-input
  width 2.5rem
</style>
