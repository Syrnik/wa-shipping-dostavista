<script setup lang="ts">
import { computed, inject, ref } from 'vue'
import LegacyWaField from './LegacyWaField.vue'
import LegacyOperatingRegionsModal from './LegacyOperatingRegionsModal.vue'
import { useNamespace } from '../composables/useNamespace'

const { addns } = useNamespace()

interface Region {
  code: string
  name: string
}

const props = withDefaults(defineProps<{
  modelValue?: string[]
  regionList?: Region[]
}>(), {
  modelValue: () => ['50', '77'],
  regionList: () => [],
})

const selection = ref(props.modelValue)
const ns = addns('operating_region', inject<string>('namespace', ''))
const modal_open = ref(false)

const selectedRegions = computed(() => props.regionList.filter(r => selection.value.includes(r.code)))
</script>

<template>
  <legacy-wa-field name="Регион доставки">
    <div class="value no-shift">
      <template v-for="(r, idx) in selectedRegions" :key="r.code">
        <input :name="addns('', ns)" :value="r.code" type="hidden">{{ r.name }}<template v-if="idx < selectedRegions.length - 2">, </template><template v-else-if="idx < selectedRegions.length - 1"> и </template>
      </template>
      <a href="#" @click.prevent="modal_open = true" class="inline-link"><b><i>изменить</i></b></a>
    </div>
    <teleport to="body">
      <legacy-operating-regions-modal v-if="modal_open" :region-list="regionList" :value="selection"
                                      @close="modal_open = false" @selected="selection = $event" />
    </teleport>
  </legacy-wa-field>
</template>
