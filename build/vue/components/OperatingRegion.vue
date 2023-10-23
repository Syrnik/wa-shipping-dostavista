<script>
import Vue from "vue";
import OperatingRegionSelect from "./operating_region/OperatingRegionSelect.vue";
import AddNs from "./wa-namespace"

export default {
    name: "OperatingRegion",

    props: {
        regionList: {type: Array, default: () => []},
        value: {type: Array, default: () => ['50', '77']},
        ns: {type: String, default: 'shipping'}
    },
    data() {
        return {
            selection: this.value,
            dialog_template: null
        }
    },

    mixins: [AddNs],

    watch: {
        selection(data) {
            this.$emit('input', data);
        }
    },

    computed: {
        selectedRegions() {
            return this.regionList.filter(r => this.selection.includes(r.code));
        }
    },

    mounted() {
        const dialog_template = $('#w-dostavista-region-dialog-template');
        this.dialog_template = dialog_template.html();
        dialog_template.remove();
    },

    methods: {
        selectRegions() {
            const dialog_template = document.getElementById('w-dostavista-region-dialog-template');
            let modal_vm, vm = this;
            $(this.dialog_template).waDialog({
                disableButtonsOnSubmit: true,
                onLoad() {
                    modal_vm = new Vue({
                        el: '#w-dostavista-region-select-dialog-content',
                        render(createElement) {
                            return createElement(OperatingRegionSelect, {
                                props: {
                                    regionList: vm.regionList,
                                    selectedRegions: vm.selection
                                }
                            })
                        }
                    })
                },
                onClose() {
                    $(this).remove();
                },
                onSubmit(dialog) {
                    vm.selection = JSON.parse(JSON.stringify(modal_vm.$children[0].$data.selection));
                    $(dialog).trigger('close');
                    return false;
                }
            });
        }
    }

}
</script>

<template>
  <wa-field name="Регион доставки">
    <div class="value no-shift">
      <template v-for="(r, idx) in selectedRegions"><input type="hidden" :name="addns('', ns)" :value="r.code">{{ r.name }}<template v-if="idx < selectedRegions.length-2">, </template><template v-else-if="idx < selectedRegions.length-1"> и </template>
      </template>
        <a href="#" @click.prevent="selectRegions()" class="inline-link"><b><i>изменить</i></b></a>
    </div>
    <div id="w-dostavista-region-dialog-template" style="display: none">
      <div id="w-dostavista-region-dialog">
        <form id="w-dostavista-region-select-form" action="">
          <div class="dialog-content">
            <div id="w-dostavista-region-select-dialog-content"></div>
          </div>
          <div class="dialog-buttons">
            <button class="green button" type="submit">Изменить</button>
            <button class="cancel button" type="button">Отмена</button>
          </div>
        </form>
      </div>
    </div>
  </wa-field>
</template>
