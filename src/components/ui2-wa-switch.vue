<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'

const props = withDefaults(defineProps<{
  modelValue?: boolean
  disabled?: boolean
}>(), {
  modelValue: true,
  disabled: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'change': [value: boolean]
  'ready': []
}>()

const switch_el = ref<HTMLElement | null>(null)
const is_ready = ref(false)
let switcher: unknown = null

onMounted(() => {
  switcher = ($ as any).waSwitch({
    $wrapper: $(switch_el.value!),
    active: props.modelValue,
    disabled: props.disabled,
    ready: () => {
      is_ready.value = true
      emit('ready')
    },
    change: (active: boolean) => {
      emit('update:modelValue', active)
      emit('change', active)
    },
  })
})

watch(() => props.modelValue, newVal => (switcher as any).set(newVal))
watch(() => props.disabled, newVal => (switcher as any).disable(newVal))
</script>

<template>
  <span ref="switch_el" class="switch"></span>
</template>
