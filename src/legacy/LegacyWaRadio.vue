<script setup lang="ts">
import LegacyWaField from './LegacyWaField.vue'
import { computed } from 'vue'

interface Option {
  value: unknown
  title: string
  description?: string
  disabled?: boolean
}

const props = defineProps<{
  name?: string
  ns?: string
  options: Option[]
  modelValue?: unknown
}>()

const emit = defineEmits<{ 'update:modelValue': [value: unknown] }>()

const val = computed({
  get() { return props.modelValue },
  set(v) { emit('update:modelValue', v) },
})
</script>

<template>
  <legacy-wa-field :name="name" name-class="for-checkbox">
    <slot name="header" />
    <div v-for="(o, idx) in options" :key="idx" :class="idx === 0 ? 'value no-shift' : 'value'">
      <label>
        <input v-model="val" :disabled="!!o.disabled" :name="ns" :value="o.value" type="radio">
        {{ o.title }}
        <span v-if="o.description && o.description.length" class="hint" v-html="'&mdash; ' + o.description"></span>
      </label>
    </div>
    <slot />
  </legacy-wa-field>
</template>
