<script setup>
import WaField from "../components/wa-field.vue";
import addns from "../components/addns";
import {inject} from "vue";

const props = defineProps({modelValue: {type: String, default: ''}});
const emit = defineEmits(['update:modelValue']);
const ns = addns('surcharge', inject('namespace'));

</script>

<template>
    <wa-field name="Корректировка стоимости доставки" name-class="for-input">
        <input type="text" class="long" :value="modelValue"
               @input="$emit('update:modelValue', $event.target.value)" :name="ns">
        <a href=""><i class="far fa-question-circle"></i></a>
        <p class="hint">Фиксированная стоимость или формула для наценки/скидки на расчётную стоимость доставки.
            Можно оставить пустым, тогда будет использоваться стоимость, посчитанная через API.</p>
    </wa-field>
</template>
