<script setup lang="ts">
import { onMounted, reactive } from 'vue'
import TokenApi from './TokenApi.vue'
import OperatingRegion from './OperatingRegion.vue'
import LocationFrom from './LocationFrom.vue'
import ServerType from './ServerType.vue'
import DeliveryTimeControl from './DeliveryTimeControl.vue'
import CustomerInterval from './CustomerInterval.vue'
import Dates from './Dates.vue'
import Insurance from './Insurance.vue'
import Surcharge from './Surcharge.vue'
import FreeDelivery from './FreeDelivery.vue'
import SmsNotifications from './SmsNotifications.vue'
import PluginHeader from './PluginHeader.vue'
import TransportType from './TransportType.vue'
import LimitsWeight from './LimitsWeight.vue'

const props = defineProps<{
  info: Record<string, unknown>
  settings: Record<string, unknown>
}>()

const setting = reactive(props.settings)

onMounted(() => document.getElementsByClassName('article')?.[0]?.classList.add('wider'))
</script>

<template>
  <div class="fields">
    <plugin-header />
    <div class="fields-group">
      <server-type v-model="setting.api_server as string" />
      <token-api v-model.trim="setting.token as string" />
      <operating-region v-model="setting.operating_region as string[]" :region-list="(info.lists as any)?.regions" />
      <location-from v-model="(setting.location_from as any).name" />
    </div>
    <div class="fields-group">
      <transport-type v-model="setting.transport_type as number" />
    </div>
    <div class="fields-group">
      <limits-weight v-model="setting.weight_limits as any" />
    </div>
    <div class="fields-group">
      <delivery-time-control v-model="setting.delivery_time as string" v-model:exact-hours="setting.exact_delivery_time as number" />
      <customer-interval v-model="setting.customer_interval as any" />
      <dates name="Дополнительные выходные" v-model="setting.holidays as string[]" field-name="holidays" class="holidays"
             button-class="green" />
      <dates name="Дополнительные рабочие дни" v-model="setting.workdays as string[]" field-name="workdays" class="workdays"
             button-class="pink" />
      <insurance v-model="setting.insurance as any" />
      <surcharge v-model.trim="setting.surcharge as string" />
      <free-delivery v-model="setting.free_delivery as number" />
      <sms-notifications v-model="setting.sms_notify as any" />
    </div>
  </div>
</template>
