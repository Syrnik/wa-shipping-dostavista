<script setup>
import WaField from "../components/wa-field.vue";
import {inject, ref} from "vue";
import addns from "../components/addns";

const props = defineProps({modelValue: {type: String, default: ''}}),
    emit = defineEmits(['update:modelValue']),
    value = ref(props.modelValue),
    ns = addns('token', inject('namespace'));

</script>

<template>
  <wa-field name="Токен API" name-class="for-input">
    <input v-model.trim="value" :name="ns" class="long" type="text" @input="$emit('update:modelValue', value)">
  </wa-field>
</template>
