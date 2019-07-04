import Vue from 'vue'
import Tabs from './vue/components/tabs'
import App from './vue/settings/App.vue'
import waField from './vue/components/wa-field.vue'

Vue.use(Tabs);
Vue.component('waField', waField);

export function run(options) {
    options = options || {};

    const v = new Vue({
        el: options.el || '#dostavista-shipping-settings',
        render(h) {
            return h(App, {props: {info: options.info || {}, settings: options.settings || {}}});
        }
    })
}