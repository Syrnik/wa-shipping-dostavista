<script>
export default {
  name: "OperatingRegionSelect",
  props: {
    regionList: {type: Array, default: () => []},
    selectedRegions: {type: Array, default: () => []}
  },
  data() {
    return {
      selection: this.selectedRegions
    }
  }
}
</script>

<template>
  <div>
    <ul class="w-shipping-dostavista-region-list">
      <li class="w-shipping-dostavista-region-list-item" v-for="r in regionList">
          <label><input type="checkbox" v-model="selection" :value="r.code">
              {{ r.name }}
          </label>
      </li>
    </ul>
  </div>
</template>
