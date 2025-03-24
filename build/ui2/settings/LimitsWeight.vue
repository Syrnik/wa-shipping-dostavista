<template>
    <min-max-limit :model-value="modelValue"
                   :ns="ns"
                   :ui="ui"
                   name="Ограничение по весу"
                   step="0.01"
                   min="0"
                   @update:modelValue="emit('update:modelValue', $event)">
        кг. <br><span class="hint">Укажите минимальный и/или максимальный вес заказа</span>
    </min-max-limit>
</template>

<script setup>
import addns from "../components/addns";
import {inject} from "vue";
import MinMaxLimit from "../components/min-max-limit.vue";

const props = defineProps({'modelValue': {type: Object, default: () => ({min: null, max: null})}}),
    emit = defineEmits(['update:modelValue']),
    ns = addns('weight_limits', inject('namespace')),
    ui = '2.0';
</script>
