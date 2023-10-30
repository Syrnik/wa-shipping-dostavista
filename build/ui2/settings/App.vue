<script setup>

import {reactive} from "vue";

const props = defineProps(['info', 'settings']);
const setting = reactive(props.settings);

</script>

<template>
  <div class="fields">

  </div>
</template>
