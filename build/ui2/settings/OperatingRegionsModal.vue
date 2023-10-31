<script setup>

import WaDialog from "../components/waDialog.vue";
import {ref} from "vue";

const props = defineProps({value: {type: Array, default: () => []}, regionList: {type: Array, default: () => []}}),
    emit = defineEmits(['selected', 'close']),
    selection = ref(props.value);
</script>

<template>
    <wa-dialog @close="$emit('close')">
        <ul class="w-shipping-dostavista-region-list">
            <li class="w-shipping-dostavista-region-list-item" v-for="r in regionList">
                <label><input type="checkbox" v-model="selection" :value="r.code">
                    {{ r.name }}
                </label>
            </li>
        </ul>
    </wa-dialog>
</template>

<style lang="stylus" scoped>

</style>
