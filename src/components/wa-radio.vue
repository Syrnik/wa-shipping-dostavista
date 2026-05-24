<script setup lang="ts">
import WaField from './wa-field.vue'
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
  <wa-field :name="name" name-class="for-checkbox">
    <slot name="header" />
    <ul>
      <li v-for="(o, idx) in options" :key="idx"><label>
        <span class="wa-radio">
          <input v-model="val" :disabled="!!o.disabled" :name="ns" :value="o.value" type="radio">
          <span></span>
        </span>
        {{ o.title }}
        <span v-if="o.description && o.description.length" class="hint" v-html="'&mdash; ' + o.description"></span>
      </label></li>
    </ul>
    <slot />
  </wa-field>
</template>
