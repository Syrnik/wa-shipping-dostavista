<template>
  <div class="zamkad-datepicker">
    <div class="zamkad-datepicker__nav">
      <button type="button" class="zamkad-datepicker__nav-btn" @click="prevMonth"><i class="fas fa-chevron-left"></i></button>
      <span class="zamkad-datepicker__title" @click="toggleYearView">
        {{ monthName }} {{ currentYear }}
      </span>
      <button type="button" class="zamkad-datepicker__nav-btn" @click="nextMonth"><i class="fas fa-chevron-right"></i></button>
    </div>

    <template v-if="!yearView">
      <div class="zamkad-datepicker__grid">
        <div v-for="d in weekdays" :key="d" class="zamkad-datepicker__weekday">{{ d }}</div>
        <div
          v-for="cell in cells"
          :key="cell.key"
          class="zamkad-datepicker__day"
          :class="{
            'zamkad-datepicker__day--empty': !cell.date,
            'zamkad-datepicker__day--selected': cell.date && isSelected(cell.date),
            'zamkad-datepicker__day--disabled': cell.date && isPast(cell.date),
          }"
          @click="cell.date && !isPast(cell.date) && toggle(cell.date)"
        >
          {{ cell.date ? cell.date.getDate() : '' }}
        </div>
      </div>
    </template>

    <template v-else>
      <div class="zamkad-datepicker__years">
        <button
          v-for="y in yearRange"
          :key="y"
          type="button"
          :class="{ 'zamkad-datepicker__year--current': y === currentYear }"
          @click="selectYear(y)"
        >{{ y }}</button>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  modelValue: string[]
  locale?: string
}>()

const emit = defineEmits<{ 'update:modelValue': [value: string[]] }>()

const today = new Date()
today.setHours(0, 0, 0, 0)

const currentYear = ref(today.getFullYear())
const currentMonth = ref(today.getMonth())
const yearView = ref(false)

const locale = computed(() => props.locale ?? 'ru')

const monthName = computed(() =>
  new Intl.DateTimeFormat(locale.value, { month: 'long', year: undefined }).format(
    new Date(currentYear.value, currentMonth.value, 1)
  )
)

const weekdays = computed(() => {
  const fmt = new Intl.DateTimeFormat(locale.value, { weekday: 'short' })
  // Monday=1 ... Sunday=0; we want Mon-Sun order
  return [1, 2, 3, 4, 5, 6, 0].map(d => {
    const date = new Date(2024, 0, d === 0 ? 7 : d)
    return fmt.format(date)
  })
})

interface Cell {
  key: string
  date: Date | null
}

const cells = computed<Cell[]>(() => {
  const year = currentYear.value
  const month = currentMonth.value
  const firstDay = new Date(year, month, 1).getDay() // 0=Sun
  // Convert to Monday-first offset (0=Mon … 6=Sun)
  const offset = (firstDay + 6) % 7
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const result: Cell[] = []
  for (let i = 0; i < offset; i++) result.push({ key: `e${i}`, date: null })
  for (let d = 1; d <= daysInMonth; d++) {
    result.push({ key: `d${d}`, date: new Date(year, month, d) })
  }
  return result
})

const yearRange = computed(() => {
  const y = currentYear.value
  const start = y - 5
  return Array.from({ length: 11 }, (_, i) => start + i)
})

function toYMD(d: Date): string {
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  return `${d.getFullYear()}-${mm}-${dd}`
}

function isSelected(d: Date): boolean {
  return props.modelValue.includes(toYMD(d))
}

function isPast(d: Date): boolean {
  return d < today
}

function toggle(d: Date): void {
  const ymd = toYMD(d)
  const next = props.modelValue.includes(ymd)
    ? props.modelValue.filter(v => v !== ymd)
    : [...props.modelValue, ymd]
  emit('update:modelValue', next)
}

function prevMonth(): void {
  if (currentMonth.value === 0) { currentYear.value--; currentMonth.value = 11 }
  else currentMonth.value--
}

function nextMonth(): void {
  if (currentMonth.value === 11) { currentYear.value++; currentMonth.value = 0 }
  else currentMonth.value++
}

function toggleYearView(): void {
  yearView.value = !yearView.value
}

function selectYear(y: number): void {
  currentYear.value = y
  yearView.value = false
}
</script>

<style lang="stylus">
.zamkad-datepicker
  display inline-block
  border 1px solid #ddd
  padding 8px
  user-select none

  &__nav
    display flex
    align-items center
    justify-content space-between
    margin-bottom 6px

  &__nav-btn
    background none !important
    border none !important
    box-shadow none !important
    padding 2px 6px !important
    margin 0 !important
    min-width 0 !important
    height auto !important
    line-height 1 !important
    cursor pointer
    font-size 12px
    color inherit
    &:hover
      background none !important
      color inherit !important
      opacity 0.6

  &__title
    cursor pointer
    font-weight bold

  &__grid
    display grid
    grid-template-columns repeat(7, 32px)
    gap 2px

  &__weekday
    text-align center
    font-size 12px
    color #888
    padding 2px 0

  &__day
    text-align center
    padding 4px 2px
    cursor pointer
    border-radius 3px
    font-size 13px
    &:hover:not(&--disabled):not(&--empty)
      background #e8f4e8
    &--selected
      background #5cb85c
      color #fff
    &--disabled
      color #ccc
      cursor default
    &--empty
      cursor default

  &__years
    display grid
    grid-template-columns repeat(3, 1fr)
    gap 4px
    button
      padding 4px
      border 1px solid #ddd
      background none
      cursor pointer
      border-radius 3px
      &:hover
        background #f0f0f0
    &__year--current
      font-weight bold
      border-color #5cb85c
</style>
