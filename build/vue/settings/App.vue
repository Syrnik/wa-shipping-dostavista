<template>
    <div id="shipping-dostavista-settings-app">
        <tabs :options="{ useUrlFragment: false }">
            <tab name="Общие настройки">
                <wa-field name="Токен API">
                    <div class="value">
                        <input type="text"
                               autocomplete="please no"
                               :name="addns('token', info.namespace)"
                               v-model="token">
                    </div>
                </wa-field>
                <location-from name="Адрес отправки"
                           :ns="addns('location_from', info.namespace)"
                           v-model="location_from"
                ></location-from>
            </tab>
            <tab name="Информация">
                фывфыв
            </tab>
        </tabs>
    </div>
</template>

<script>
    import AddNs from '../components/wa-namespace'
    import LocationFrom from './LocationFrom.vue'

    export default {
        props: {
            info: {type: Object, required: true},
            settings: {type: Object, required: true}
        },
        mixins: [AddNs],
        data() {
            return this.settings
        },
        components: {LocationFrom}
    }
</script>
