<script>
import Addns from "../components/wa-namespace";

export default {
    name: "MinMaxLimits",
    mixins: [Addns],
    props: {
        name: {type: String, default: ""},
        ns: {type: String, default: ""},
        value: {type: Object, default: () => ({min: 0, max: 0})},
        step: {type: Number, default: 0.001},
    },
    data() {
        return {
            setting: this.value,
            step: 1,
        }
    }
}
</script>

<template>
    <wa-field :name="name">
        <div class="value">
            от <input v-model.number="setting.min"
                      :name="addns('min', ns)"
                      :step="step"
                      class="short numerical"
                      min="0"
                      placeholder="0"
                      type="number"
                      @input="$emit('input', setting)">
            до <input v-model.number="setting.max"
                      :name="addns('max', ns)"
                      :step="step"
                      class="short numerical"
                      min="0"
                      placeholder="∞"
                      type="number"
                      @input="$emit('input', setting)">
            <slot/>
        </div>
    </wa-field>

</template>
