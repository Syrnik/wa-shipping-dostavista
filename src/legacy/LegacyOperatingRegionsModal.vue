<script setup lang="ts">
import WaDialog from '../components/waDialog.vue'
import { ref } from 'vue'

interface Region {
  code: string
  name: string
}

const props = withDefaults(defineProps<{
  value?: string[]
  regionList?: Region[]
}>(), {
  value: () => [],
  regionList: () => [],
})

const emit = defineEmits<{
  'selected': [value: string[]]
  'close': []
}>()

const selection = ref([...props.value])

function submit() {
  emit('selected', JSON.parse(JSON.stringify(selection.value)))
  emit('close')
}
</script>

<template>
  <wa-dialog @close="$emit('close')" class="w-shipping-dostavista-operating-region-dialog">
    <template #header><h3>Выберите регион</h3></template>
    <template #footer>
      <button class="button green" @click.prevent="submit">Выбрать</button>
      <button class="button" @click.prevent="$emit('close')">Отмена</button>
    </template>
    <ul class="w-shipping-dostavista-region-list">
      <li class="w-shipping-dostavista-region-list-item" v-for="r in regionList" :key="r.code">
        <label>
          <input type="checkbox" v-model="selection" :value="r.code">
          {{ r.name }}
        </label>
      </li>
    </ul>
  </wa-dialog>
</template>

<style lang="stylus">
.w-shipping-dostavista-operating-region-dialog

  .w-shipping-dostavista-region-list
    list-style-type none

    @media screen and (min-width: 1280px)
      column-count 3
      column-gap 1em

    @media screen and (min-width: 960px) and (max-width: 1279px)
      column-count 2
      column-gap 0.75em

  .w-shipping-dostavista-region-list-item
    padding 0.25em 3px

    label
      display block
</style>
