<script setup>

import WaDialog from "../components/waDialog.vue";
import {ref, unref} from "vue";

const props = defineProps({value: {type: Array, default: () => []}, regionList: {type: Array, default: () => []}}),
    emit = defineEmits(['selected', 'close']),
    selection = ref(props.value);

function submit() {
  emit('selected', JSON.parse(JSON.stringify(selection.value)));
  emit('close');
}
</script>

<template>
  <wa-dialog @close="$emit('close')" class="w-shipping-dostavista-operating-region-dialog">
    <template #header><h3>Выберите регион</h3></template>
    <template #footer>
      <button class="button" @click.prevent="submit">Выбрать</button>
      <button class="button gray" @click.prevent="$emit('close')">отмена</button>
    </template>
    <ul class="w-shipping-dostavista-region-list">
      <li class="w-shipping-dostavista-region-list-item" v-for="r in regionList">
        <label>
            <span class="wa-checkbox">
                <input type="checkbox" v-model="selection" :value="r.code">
                <span>
                    <span class="icon">
                        <i class="fas fa-check"></i>
                    </span>
                </span>
            </span>
          {{ r.name }}
        </label>
      </li>
    </ul>
  </wa-dialog>
</template>

<style lang="stylus">
.w-shipping-dostavista-operating-region-dialog

  &.dialog
    .dialog-body
      left 0
      right 0
      top 0
      bottom 0
      width auto

  .w-shipping-dostavista-region-list
    list-style-type none

    @media screen and (min-width: 1280px)
      column-count: 3;
      column-gap: 1em;

    @media screen and (min-width: 960px) and (max-width: 1279px)
      column-count: 2;
      column-gap: 0.75em;

  .w-shipping-dostavista-region-list-item
    padding 0.25em 3px

    label
      display block
</style>
