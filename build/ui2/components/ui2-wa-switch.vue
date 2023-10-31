<script setup>
import {onMounted, ref, watch} from "vue";

const props = defineProps({
  modelValue: {type: Boolean, default: true},
  disabled: {type: Boolean, default: false},
});
const emit = defineEmits(['update:modelValue', 'change', 'ready']);
const switch_el = ref(null);
const is_ready = ref(false);
let switcher = null;

onMounted(() => {
  switcher = $.waSwitch({
    $wrapper: $(switch_el.value),
    active: props.modelValue,
    disabled: props.disabled,
    ready: () => {
      is_ready.value = true;
      emit('ready');
    },
    change: active => {
      emit('update:modelValue', active);
      emit('change', active);
    }
  });
});

watch(() => props.modelValue, newVal => switcher.set(newVal));
watch(() => props.disabled, newVal => switcher.disable(newVal));

</script>

<template>
  <span ref="switch_el" class="switch"></span>
</template>
