<script setup>

import {reactive} from "vue";
import TokenApi from "./TokenApi.vue";
import OperatingRegion from "./OperatingRegion.vue";
import LocationFrom from "./LocationFrom.vue";

const props = defineProps(['info', 'settings']);
const setting = reactive(props.settings);

</script>

<template>
  <div class="fields">
    <div class="fields-group">
      <token-api v-model.trim="setting.token"/>
      <operating-region v-model="setting.operating_regions" :region-list="info.lists.regions"/>
      <location-from v-model="setting.location_from.name"/>
    </div>
  </div>
</template>
