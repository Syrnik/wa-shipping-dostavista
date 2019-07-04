<template>
    <div id="shipping-neograstin-settings-app">
        <tabs :options="{ useUrlFragment: false }">
            <tab name="Общие настройки">
                <wa-field name="Город отправления">
                    <div class="value">
                        <select :name="addns('from_city', info.namespace)" v-model="from_city">
                            <option value="Москва">Москва</option>
                            <option value="Санкт-Петербург">Санкт-Петербург</option>
                            <option value="Нижний Новгород">Нижний Новгород</option>
                        </select></div>
                </wa-field>
            </tab>
            <tab name="Информация">
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
        components: {Services, About},
        data() {
            return this.settings
        }
    }
</script>
