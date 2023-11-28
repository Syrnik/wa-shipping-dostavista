<script setup>
import WaField from "../components/wa-field.vue";
import Ui2Checkbox from "../components/ui2-checkbox.vue";
import {computed, inject, ref} from "vue";
import addns from "../components/addns";

const props = defineProps({modelValue: {type: Object}}),
  emit = defineEmits(['update:modelValue']),
  setting = ref(props.modelValue),
  namespace = inject('namespace'),
  interval_row = {
    from: '10',
    from_m: '00',
    to: '12',
    to_m: '00',
    day: [1, 2, 3, 4, 5],
    workday: false,
    holiday: false
  },
  date_value = computed({
    get: () => setting.value.date ? '1' : '0',
    set: v => setting.value.date = v === '1'

  }),
  interval_value = computed({
    get: () => setting.value.interval ? '1' : '0',
    set: v => setting.value.interval = v === '1'
  }),
  intervals_ns = addns('intervals', namespace);

function emitChanges() {
  emit('update:modelValue', JSON.parse(JSON.stringify(setting.value)));
}

function addInterval() {
    setting.value.intervals.push(Object.assign({}, interval_row));
  emitChanges();
}

function deleteInterval(idx) {
  if (setting.value.intervals.length > 1) setting.value.intervals.splice(idx, 1);
  else setting.value.intervals[0] = Object.assign({}, interval_row);
  emitChanges();
}
</script>

<template>
  <wa-field name="Желаемое время доставки и график работы" name-class="for-checkbox">
    <ul>
      <li>
        <ui2-checkbox :name="addns('date', namespace)" v-model="date_value"
                      @update:model-value="$nextTick().then(emitChanges)">
          Запрашивать желаемую дату доставки
        </ui2-checkbox>
      </li>
      <li>
        <ui2-checkbox :name="addns('interval', namespace)" v-model="interval_value"
                      @update:model-value="$nextTick().then(emitChanges)">
          Запрашивать желаемый интервал доставки
        </ui2-checkbox>
      </li>
    </ul>
    <div v-show="setting.interval">
      <table class="zebra w-shipping-dostavista__interval-control-table small">
        <thead>
        <tr>
          <th colspan="2">Интервалы доставки</th>
          <th v-for="v in ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']">{{ v }}</th>
          <th>Доп. выходной</th>
          <th>Доп. рабочий день</th>
          <th class="min-width"></th>
        </tr>
        </thead>
        <tfoot>
        <tr class="white">
          <td colspan="12"><a class="inline-link" @click.prevent="addInterval">
            <i class="icon16 add fa fa-plus-circle text-green"></i>
            Добавить интервал</a>
          </td>
        </tr>
        </tfoot>
        <tbody>
        <tr v-for="(interval, idx) in setting.intervals">
          <td class="nowrap">с
            <input type="text" class="shortest w-shipping-dostavista__hour-minute-input align-right"
                   :name="addns('from', addns(idx, intervals_ns))"
                   v-model.trim="setting.intervals[idx].from" @input="emitChanges"> :
            <input type="text" class="shortest align-right w-shipping-dostavista__hour-minute-input"
                   :name="addns('from_m', addns(idx, intervals_ns))"
                   v-model.trim="setting.intervals[idx].from_m" @input="emitChanges">
          </td>
          <td class="nowrap">до
            <input type="text" class="shortest w-shipping-dostavista__hour-minute-input align-right"
                   :name="addns('to', addns(idx, intervals_ns))"
                   v-model.trim="setting.intervals[idx].to" @input="emitChanges"> :
            <input type="text" class="shortest align-right w-shipping-dostavista__hour-minute-input"
                   :name="addns('to_m', addns(idx, intervals_ns))"
                   v-model.trim="setting.intervals[idx].to_m" @input="emitChanges">
          </td>
          <td v-for="v in [1,2,3,4,5,6,7]"><input type="checkbox"
                                                  :name="addns('', addns('day', addns(idx, intervals_ns)))" :value="v"
                                                  v-model="setting.intervals[idx].day" @change="emitChanges"></td>
          <td><input type="checkbox" :name="addns('holiday', addns(idx, intervals_ns))"
                     v-model="setting.intervals[idx].holiday" @change="emitChanges"></td>
          <td><input type="checkbox" :name="addns('workday', addns(idx, intervals_ns))"
                     v-model="setting.intervals[idx].workday" @change="emitChanges"></td>
          <td><a class="inline-link"
                 @click.prevent="deleteInterval(idx)"><i class="icon16 delete fa fa-trash-alt text-red"></i></a></td>
        </tr>
        </tbody>
      </table>
    </div>
  </wa-field>
</template>

<style lang="stylus">
.w-shipping-dostavista__interval-control-table

  thead
    tr
      th
        width 100%

input.w-shipping-dostavista__hour-minute-input
  width 2.5rem
</style>
