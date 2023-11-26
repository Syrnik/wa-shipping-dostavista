<script setup>
import WaRadio from "../components/wa-radio.vue";
import {inject, ref} from "vue";
import addns from "../components/addns";

const props = defineProps({
    modelValue: {type: Object, default: () => ({type: "none", value: ""})}
});

const emit = defineEmits(['update:modelValue']);
const ns = addns('insurance', inject('namespace'));

const value = ref(props.modelValue);

const options = [
    {value: 'none', title: 'Без страховки'},
    {value: 'raw', title: 'Стоимость товара без скидок'},
    {value: 'total', title: 'Стоимость товара со скидками'},
    {value: 'custom', title: 'Фиксированная или формула'}
];

</script>

<template>
    <wa-radio name="Сумма страховки" :options="options" v-model="value.type" @update:model-value="$emit('update:modelValue', value)"
              :ns="addns('type',ns)">
        <template v-if="value.type==='custom'">
            <input type="text" class="long" :name="addns('value', ns)" v-model.trim="value.value"
                   @input="$emit('update:modelValue',value)">
            <a href=""><i class="far fa-question-circle"></i></a>
        </template>
    </wa-radio>
</template>
