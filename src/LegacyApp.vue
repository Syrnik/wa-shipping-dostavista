<script setup lang="ts">
import { onMounted, reactive } from 'vue'
import TokenApi from './settings/TokenApi.vue'
import LocationFrom from './settings/LocationFrom.vue'
import LimitsWeight from './settings/LimitsWeight.vue'
import LegacyOperatingRegion from './legacy/LegacyOperatingRegion.vue'
import LegacyServerType from './legacy/LegacyServerType.vue'
import LegacyTransportType from './legacy/LegacyTransportType.vue'
import LegacyDeliveryTimeControl from './legacy/LegacyDeliveryTimeControl.vue'
import LegacyCustomerInterval from './legacy/LegacyCustomerInterval.vue'
import LegacyDates from './legacy/LegacyDates.vue'
import LegacyInsurance from './legacy/LegacyInsurance.vue'
import LegacySurcharge from './legacy/LegacySurcharge.vue'
import LegacyFreeDelivery from './legacy/LegacyFreeDelivery.vue'
import LegacySmsNotifications from './legacy/LegacySmsNotifications.vue'

const props = defineProps<{
  info: Record<string, unknown>
  settings: Record<string, unknown>
}>()

const setting = reactive(props.settings)

onMounted(() => document.getElementsByClassName('article')?.[0]?.classList.add('wider'))
</script>

<template>
  <div class="fields">
    <legacy-server-type v-model="setting.api_server as string" />
    <token-api v-model.trim="setting.token as string" />
    <legacy-operating-region v-model="setting.operating_region as string[]" :region-list="(info.lists as any)?.regions" />
    <location-from v-model="(setting.location_from as any).name" />
    <legacy-transport-type v-model="setting.transport_type as number" />
    <limits-weight v-model="setting.weight_limits as any" />
    <legacy-delivery-time-control v-model="setting.delivery_time as string" v-model:exact-hours="setting.exact_delivery_time as number" />
    <legacy-customer-interval v-model="setting.customer_interval as any" />
    <legacy-dates name="Дополнительные выходные" v-model="setting.holidays as string[]" field-name="holidays" class="holidays" />
    <legacy-dates name="Дополнительные рабочие дни" v-model="setting.workdays as string[]" field-name="workdays" class="workdays" />
    <legacy-insurance v-model="setting.insurance as any" />
    <legacy-surcharge v-model.trim="setting.surcharge as string" />
    <legacy-free-delivery v-model="setting.free_delivery as number" />
    <legacy-sms-notifications v-model="setting.sms_notify as any" />
  </div>
</template>
