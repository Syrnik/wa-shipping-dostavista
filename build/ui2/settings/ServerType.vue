<script setup>
import addns from "../components/addns";
import {inject} from "vue";
import WaRadio from "../components/wa-radio.vue";

const props = defineProps({modelValue: String, default: 'test'}),
    ns = addns('api_server', inject('namespace')),
    options = [
      {value: 'test', title: 'Тестовый'},
      {value: 'production', title: 'Рабочий'}
    ];

defineEmits(['update:modelValue']);
</script>

<template>
  <wa-radio :options="options" :ns="ns" name="Сервер API" :model-value="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"/>
</template>
