<script setup>
import {computed, inject, ref} from "vue";
import WaField from "../components/wa-field.vue";
import addns from "../components/addns";
import OperatingRegionsModal from "./OperatingRegionsModal.vue";

const props = defineProps({
    modelValue: {type: Array, default: () => ['50', '77']},
    regionList: {type: Array, default: () => []}
});
const selection = ref(props.modelValue),
    ns = addns('operating_regions', inject('namespace'));
let dialog_html,
    model_selection = ref([]), modal_open = ref(false);

const selectedRegions = computed(() => props.regionList.filter(r => selection.value.includes(r.code)));

</script>

<template>
    <wa-field name="Регион доставки">
        <template v-for="(r, idx) in selectedRegions"><input :name="addns('', ns)" :value="r.code"
                                                             type="hidden">{{ r.name }}
            <template v-if="idx < selectedRegions.length-2">,</template>
            <template v-else-if="idx < selectedRegions.length-1"> и</template>
        </template>
        <button class="button nobutton" href="#" type="button" @click="modal_open=true">изменить</button>
        <teleport to="body">
            <operating-regions-modal v-if="modal_open" :region-list="regionList" :value="selection"
                                     @close="modal_open=false"/>
        </teleport>
    </wa-field>
</template>
