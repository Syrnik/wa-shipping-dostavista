<script setup>
import WaField from "./wa-field.vue";
import {computed} from "vue";

const props = defineProps({
  name: String,
  ns: {type: String, default: ''},
  options: {type: Array, required: true},
  modelValue: {}
});

const emit = defineEmits(['update:modelValue']);

const val = computed({
  get() {
    return props.modelValue;
  },
  set(v) {
    emit('update:modelValue', v);
  }
});

</script>

<template>
  <wa-field :name="name" name-class="for-checkbox">
    <slot name="header"/>
    <ul>
      <li v-for="(o, idx) in options"><label>
          <span class="wa-radio">
              <input v-model="val" :disabled="!!o.disabled" :name="ns" :value="o.value" type="radio">
              <span></span>
          </span>
        {{ o.title }}
        <span v-if="o.description && o.description.length" class="hint" v-html="'&mdash; ' + o.description"></span>
      </label></li>
    </ul>
    <slot/>
  </wa-field>
</template>
