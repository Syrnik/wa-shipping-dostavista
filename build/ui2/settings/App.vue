<script setup>

import {onMounted, reactive} from "vue";
import TokenApi from "./TokenApi.vue";
import OperatingRegion from "./OperatingRegion.vue";
import LocationFrom from "./LocationFrom.vue";
import ServerType from "./ServerType.vue";
import DeliveryTimeControl from "./DeliveryTimeControl.vue";
import CustomerInterval from "./CustomerInterval.vue";

const props = defineProps(['info', 'settings']);
const setting = reactive(props.settings);

onMounted(()=> document.getElementsByClassName('article')?.[0]?.classList.add('wider'));

</script>

<template>
  <div class="fields">
    <div class="fields-group">
      <server-type v-model="setting.api_server"/>
      <token-api v-model.trim="setting.token"/>
      <operating-region v-model="setting.operating_regions" :region-list="info.lists.regions"/>
      <location-from v-model="setting.location_from.name"/>
    </div>
    <div class="fields-group">
      <delivery-time-control v-model="setting.delivery_time" v-model:exact-hours="setting.exact_delivery_time"/>
        <customer-interval v-model="setting.customer_interval" />
    </div>
  </div>
</template>
