<script setup>
import addns from "../components/addns";
import {computed, inject, ref} from "vue";
import WaField from "../components/wa-field.vue";
import Ui2Checkbox from "../components/ui2-checkbox.vue";

const props = defineProps({modelValue: {type: Object, default: () => ({client: false, receiver: "no"})}});
const emit = defineEmits(['update:modelValue']);
const ns = addns('sms_notify', inject('namespace'));
const value = ref(props.modelValue);
const client = computed({
    get() {
        return value.value.client ? '1' : '0'
    },
    set(v) {
        value.value.client = !!v;
        emit('update:modelValue', value);
    }
});
</script>

<template>
    <wa-field name="SMS-уведомления" name-class="for-checkbox">
        <div>
            <ui2-checkbox v-model="client" :name="addns('client', ns)">
                — отправлять в магазин SMS о статусе заказа
            </ui2-checkbox>
        </div>
        <div>
            <ui2-checkbox v-model="value.receiver" false-value="no" true-value="yes" :name="addns('receiver', ns)"
                          @update:model-value="$emit('update:modelValue', value)">
                — отправлять получателю SMS с интервалом прибытия и телефоном курьера
            </ui2-checkbox>
        </div>
    </wa-field>
</template>
