<script setup>

import {reactive} from "vue";
import TokenApi from "./TokenApi.vue";
import OperatingRegion from "./OperatingRegion.vue";

const props = defineProps(['info', 'settings']);
const setting = reactive(props.settings);

</script>

<template>
  <div class="fields">
      <token-api v-model.trim="setting.token" />
      <operating-region v-model="setting.operating_regions" :region-list="info.lists.regions" />
  </div>
</template>
