<script setup lang="ts">
import { computed, inject, ref } from 'vue'
import LegacyWaField from './LegacyWaField.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

interface SmsNotifySetting {
  client: boolean
  receiver: string
}

const props = withDefaults(defineProps<{
  modelValue?: SmsNotifySetting
}>(), {
  modelValue: () => ({ client: false, receiver: 'no' }),
})

const emit = defineEmits<{ 'update:modelValue': [value: SmsNotifySetting] }>()

const ns = addns('sms_notify', inject<string>('namespace', ''))
const value = ref({ ...props.modelValue })
</script>

<template>
  <legacy-wa-field name="SMS-уведомления" name-class="for-checkbox">
    <div class="value no-shift">
      <input type="hidden" :name="addns('client', ns)" value="0">
      <label>
        <input type="checkbox" :name="addns('client', ns)" value="1" v-model="value.client"
               @change="$emit('update:modelValue', value)">
        &mdash; отправлять в магазин SMS о статусе заказа
      </label>
    </div>
    <div class="value">
      <input type="hidden" :name="addns('receiver', ns)" value="no">
      <label>
        <input type="checkbox" :name="addns('receiver', ns)" value="yes" v-model="value.receiver"
               @change="$emit('update:modelValue', value)">
        &mdash; отправлять получателю SMS с интервалом прибытия и телефоном курьера
      </label>
    </div>
  </legacy-wa-field>
</template>
