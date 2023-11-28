<script setup>
import WaField from "../components/wa-field.vue";
import {inject, ref} from "vue";
import addns from "../components/addns";

const props = defineProps({modelValue: {type: String, default: ''}});
defineEmits(['update:modelValue']);

const address = ref(props.modelValue),
    ns = addns('name', addns('location_from', inject('namespace')));

</script>

<template>
  <wa-field name="Адрес отправки" name-class="for-input">
    <input type="text" class="long" v-model.trim="address" :name="ns" :class="{'state-error':!address.length}">
    <p class="hint">Укажите адрес, от которого отправляется курьер. Город, улица и дом.</p>
  </wa-field>
</template>
