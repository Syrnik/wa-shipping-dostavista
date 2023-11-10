<script setup>
const props = defineProps({
  name: {type: String, default: ''},
  falseValue: {default: '0'},
  trueValue: {default: '1'},
  modelValue: {}
});
const emit = defineEmits(['update:modelValue']);
</script>

<template>
  <input :name="name" :value="falseValue" type="hidden">
  <label>
      <span class="wa-checkbox">
          <input :checked="modelValue===trueValue" :name="name" :value="trueValue" type="checkbox"
                 @change="emit('update:modelValue', !!$event.target.checked ? trueValue : falseValue)">
          <span>
              <span class="icon">
                  <i class="fas fa-check"></i>
              </span>
          </span>
      </span>
      <slot />
  </label>
</template>
