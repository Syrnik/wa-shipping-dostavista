<script setup lang="ts">
import LegacyWaField from './LegacyWaField.vue'
import { inject, ref } from 'vue'
import { useNamespace } from '../composables/useNamespace'
import { json_clone } from '../utils'

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

interface CustomerIntervalSetting {
  date: boolean
  interval: boolean
  intervals: IntervalRow[]
}

const props = defineProps<{ modelValue: CustomerIntervalSetting }>()
const emit = defineEmits<{ 'update:modelValue': [value: CustomerIntervalSetting] }>()

const setting = ref(json_clone(props.modelValue))
const namespace = addns('customer_interval', inject<string>('namespace', ''))
const intervals_ns = addns('intervals', namespace)

const interval_row: IntervalRow = {
  from: '10', from_m: '00', to: '12', to_m: '00',
  day: [1, 2, 3, 4, 5], workday: false, holiday: false,
}

function emitChanges() {
  emit('update:modelValue', json_clone(setting.value))
}

function addInterval() {
  setting.value.intervals.push({ ...interval_row, day: [...interval_row.day] })
  emitChanges()
}

function deleteInterval(idx: number) {
  const intervals = json_clone(setting.value.intervals)
  if (intervals.length > 1) {
    intervals.splice(idx, 1)
  } else {
    intervals[0] = { ...interval_row, day: [...interval_row.day] }
  }
  setting.value.intervals = intervals
  emitChanges()
}
</script>

<template>
  <legacy-wa-field name="Желаемое время доставки и график работы" name-class="for-checkbox">
    <div class="value no-shift">
      <input type="hidden" :name="addns('date', namespace)" value="0">
      <label>
        <input type="checkbox" :name="addns('date', namespace)" value="1" v-model="setting.date"
               @change="$nextTick().then(() => emitChanges())">
        Запрашивать желаемую дату доставки
      </label>
    </div>
    <div class="value">
      <input type="hidden" :name="addns('interval', namespace)" value="0">
      <label>
        <input type="checkbox" :name="addns('interval', namespace)" value="1" v-model="setting.interval"
               @change="$nextTick().then(() => emitChanges())">
        Запрашивать желаемый интервал доставки
      </label>
    </div>
    <div class="value" v-show="setting.interval">
      <table class="zebra">
        <thead>
          <tr>
            <th colspan="2">Интервалы доставки</th>
            <th v-for="v in ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']" :key="v">{{ v }}</th>
            <th>Доп. выходной</th>
            <th>Доп. рабочий день</th>
            <th class="min-width"></th>
          </tr>
        </thead>
        <tfoot>
          <tr class="white">
            <td colspan="12">
              <a class="inline-link" @click.prevent="addInterval()">
                <i class="icon16 add"></i><b><i>Добавить интервал</i></b>
              </a>
            </td>
          </tr>
        </tfoot>
        <tbody>
          <tr v-for="(interval, idx) in setting.intervals" :key="idx">
            <td class="nowrap">с
              <input type="text" class="short numerical"
                     :name="addns('from', addns(idx, intervals_ns))"
                     v-model.trim="setting.intervals[idx].from" @input="emitChanges()"> :
              <input type="text" class="short numerical"
                     :name="addns('from_m', addns(idx, intervals_ns))"
                     v-model.trim="setting.intervals[idx].from_m" @input="emitChanges()">
            </td>
            <td class="nowrap">до
              <input type="text" class="short numerical"
                     :name="addns('to', addns(idx, intervals_ns))"
                     v-model.trim="setting.intervals[idx].to" @input="emitChanges()"> :
              <input type="text" class="short numerical"
                     :name="addns('to_m', addns(idx, intervals_ns))"
                     v-model.trim="setting.intervals[idx].to_m" @input="emitChanges()">
            </td>
            <td v-for="v in [1, 2, 3, 4, 5, 6, 7]" :key="v">
              <input type="checkbox"
                     :name="addns('', addns('day', addns(idx, intervals_ns)))"
                     :value="v"
                     v-model="setting.intervals[idx].day" @change="emitChanges()">
            </td>
            <td><input type="checkbox" :name="addns('holiday', addns(idx, intervals_ns))"
                       v-model="setting.intervals[idx].holiday" @change="emitChanges()"></td>
            <td><input type="checkbox" :name="addns('workday', addns(idx, intervals_ns))"
                       v-model="setting.intervals[idx].workday" @change="emitChanges()"></td>
            <td><a class="inline-link" @click.prevent="deleteInterval(idx)">
              <i class="icon16 delete"></i></a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </legacy-wa-field>
</template>
