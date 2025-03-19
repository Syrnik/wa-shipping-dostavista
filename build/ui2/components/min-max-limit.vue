<template>
    <wa-field>
        от <input v-model.number="setting.min" :class="ui==='2.0' ? 'shortest':'short'" :name="addns('min', ns)"
                  :step="step" class="numerical" min="0"
                  placeholder="0"
                  type="number"
                  @input="$emit('update:modelValue', setting)">
        до <input v-model.number="setting.max" :class="ui==='2.0' ? 'shortest':'short'" :name="addns('max', ns)"
                  :step="step"
                  class="numerical" min="0"
                  placeholder="∞"
                  type="number"
                  @input="$emit('update:modelValue', setting)">
        <slot/>
    </wa-field>
</template>

<script>
export default {
    name: "MinMaxLimit"
}
</script>

<script setup>

import addns from './addns'
import {ref} from "vue";
import WaField from "./wa-field.vue";

const props = defineProps({
    modelValue: {},
    ns: String,
    step: {},
    ui: {type: String, default: '2.0'}
});
const emit = defineEmits(['update:modelValue']);
const setting = ref(props.modelValue);

</script>
