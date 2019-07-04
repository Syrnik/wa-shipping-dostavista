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
            </tab>
            <tab name="Информация">
                фывфыв
            </tab>
        </tabs>
    </div>
</template>

<script>
    import AddNs from '../components/wa-namespace'

    export default {
        props: {
            info: {type: Object, required: true},
            settings: {type: Object, required: true}
        },
        mixins: [AddNs],
        data() {
            return this.settings
        }
    }
</script>
