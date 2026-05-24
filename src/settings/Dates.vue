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
  buttonClass?: string
}>(), {
  name: '',
  fieldName: '',
  modelValue: () => [],
  buttonClass: '',
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
    <ul class="w-shipping-syrnik-dates-list" v-if="sortedDates.length">
      <li v-for="(ymd, idx) in sortedDates" :key="ymd">
        <span class="smallest button nowrap" :class="buttonClass">{{ dmyDate(ymd) }}
          <a href="#" style="color: var(--red)" class="custom-ml-4" @click.prevent="deleteDate(ymd)">
            <i class="red fas fa-times"></i>
          </a>
        </span>
        <input type="hidden" :name="addns(idx, ns)" :value="ymd">
      </li>
    </ul>
    <div :class="{ 'custom-mt-12': sortedDates.length }">
      <button type="button" class="small button" @click="calendar_open = !calendar_open">
        <i class="far fa-calendar-alt"></i>
        {{ calendar_open ? 'Закрыть' : 'Выбрать' }}
      </button>
      <div v-if="calendar_open" style="position:absolute;z-index:100;margin-top:4px;background:Canvas;box-shadow:0 4px 16px rgba(0,0,0,.15);border-radius:4px">
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
.w-shipping-syrnik-dates-list
  display block
  margin 0
  list-style-type none
  box-sizing border-box
  padding 0

  li
    box-sizing border-box
    margin 0 0.3em 0 0
    display inline-block

.workdays .zamkad-datepicker__day--selected
  background #d9534f
  color #fff
</style>
