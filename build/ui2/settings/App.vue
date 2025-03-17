<script setup>

import {onMounted, reactive} from "vue";
import TokenApi from "./TokenApi.vue";
import OperatingRegion from "./OperatingRegion.vue";
import LocationFrom from "./LocationFrom.vue";
import ServerType from "./ServerType.vue";
import DeliveryTimeControl from "./DeliveryTimeControl.vue";
import CustomerInterval from "./CustomerInterval.vue";
import Dates from "./Dates.vue";
import Insurance from "./Insurance.vue";
import Surcharge from "./Surcharge.vue";
import FreeDelivery from "./FreeDelivery.vue";
import SmsNotifications from "./SmsNotifications.vue";
import PluginHeader from "./PluginHeader.vue";
import TransportType from "./TransportType.vue";

const props = defineProps(['info', 'settings']);
const setting = reactive(props.settings);

onMounted(() => document.getElementsByClassName('article')?.[0]?.classList.add('wider'));

</script>

<template>
    <div class="fields">
        <plugin-header />
        <div class="fields-group">
            <server-type v-model="setting.api_server"/>
            <token-api v-model.trim="setting.token"/>
            <operating-region v-model="setting.operating_region" :region-list="info.lists.regions"/>
            <location-from v-model="setting.location_from.name"/>
        </div>
        <div class="fields-group">
            <transport-type v-model="setting.transport_type"/>
        </div>
        <div class="fields-group"></div>
        <div class="fields-group">
            <delivery-time-control v-model="setting.delivery_time" v-model:exact-hours="setting.exact_delivery_time"/>
            <customer-interval v-model="setting.customer_interval"/>
            <dates name="Дополнительные выходные" v-model="setting.holidays" field-name="holidays" class="holidays"
                   button-class="green"/>
            <dates name="Дополнительные рабочие дни" v-model="setting.workdays" field-name="workdays" class="workdays"
                   button-class="pink"/>
            <insurance v-model="setting.insurance"/>
            <surcharge v-model.trim="setting.surcharge"/>
            <free-delivery v-model="setting.free_delivery"/>
            <sms-notifications v-model="setting.sms_notify"/>
        </div>
    </div>
</template>
