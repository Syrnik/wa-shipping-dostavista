<script setup lang="ts">
import { computed, inject, ref } from 'vue'
import dateFormat from 'dateformat'
import WaField from '../components/wa-field.vue'
import DatePickerMultiple from '../components/DatePickerMultiple.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

const props = withDefaults(defineProps<{
  name?: string
  fieldName?: string
  modelValue?: string[]
}>(), {
  name: '',
  fieldName: '',
  modelValue: () => [],
})

const emit = defineEmits<{ 'update:modelValue': [value: string[]] }>()

const ns = addns(props.fieldName, inject<string>('namespace', ''))
const calendar_open = ref(false)

const sortedDates = computed(() => [...props.modelValue].sort())

const currentLocale = computed(() => {
  const loc = (window as unknown as Record<string, string>).$_syrnik_current_locale ?? 'ru_RU'
  return loc.replace('_', '-')
})

function dmyDate(ymd: string): string {
  const d = new Date(ymd)
  if (isNaN(d.getTime())) return ''
  return dateFormat(d, 'dd.mm.yyyy')
}

function deleteDate(ymd: string): void {
  emit('update:modelValue', props.modelValue.filter(v => v !== ymd))
}
</script>

<template>
  <wa-field :name="name">
    <ul class="vue-dates-list" v-if="sortedDates.length">
      <li v-for="(ymd, idx) in sortedDates" :key="ymd">
        {{ dmyDate(ymd) }} <a href="#" @click.prevent="deleteDate(ymd)"><i class="icon10 no"></i></a>
        <input type="hidden" :name="addns(idx, ns)" :value="ymd">
      </li>
    </ul>
    <div style="position: relative">
      <button type="button" @click="calendar_open = !calendar_open">
        <i class="icon16 calendar"></i>
        {{ calendar_open ? 'Закрыть' : 'Выбрать' }}
      </button>
      <div v-if="calendar_open" style="position: absolute; z-index: 100; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,.2)">
        <DatePickerMultiple
          :modelValue="modelValue"
          :locale="currentLocale"
          @update:modelValue="emit('update:modelValue', $event)"
        />
      </div>
    </div>
  </wa-field>
</template>

<style lang="stylus">
.vue-dates-list
  display inline
  margin 0
  list-style-type none
  box-sizing border-box
  padding 0

  li
    box-sizing border-box
    margin 0 0.3em 0 0
    padding 3px
    border 1px solid transparent
    font-size 12px
    border-radius 3px
    line-height 12px
    height 20px
    display inline-block

    a
      vertical-align middle
      display inline-block
      box-sizing border-box

      i.icon10
        margin 0
        vertical-align initial
        line-height 10px
        box-sizing border-box

.holidays .vue-dates-list li
  background-color #cfc
  border-color lightgreen

.workdays .vue-dates-list li
  background-color #fcc
  border-color rosybrown
</style>
