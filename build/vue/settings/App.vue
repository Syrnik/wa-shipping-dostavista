<template>
    <div id="shipping-dostavista-settings-app">
        <tabs :options="{ useUrlFragment: false }">
            <tab name="Общие настройки">
                <wa-field name="Токен API">
                    <div class="value">
                        <input type="text"
                               autocomplete="please no"
                               :name="addns('token', info.namespace)"
                               v-model="token"></div></wa-field>
                <location-from name="Адрес отправки"
                           :ns="addns('location_from', info.namespace)"
                           v-model="location_from"></location-from>
                <wa-field name="Сервер API">
                    <div class="value no-shift">
                        <label><input type="radio" :name="addns('api_server', info.namespace)" v-model="api_server" value="test"> Тестовый</label></div>
                    <div class="value">
                        <label><input type="radio" :name="addns('api_server', info.namespace)" v-model="api_server" value="production"> Рабочий</label></div>
                </wa-field>
                <delivery-time v-model="delivery_time" :ns="addns('delivery_time', info.namespace)"></delivery-time>
                <wa-field name="Среднее количество часов доставки" v-show="delivery_time === 'exact_delivery_time'">
                    <div class="value">
                        <input type="number"
                               class="short numerical"
                               :name="addns('exact_delivery_time', info.namespace)"
                               v-model.number="exact_delivery_time"><br>
                        <span class="hint">Среднее время в часах, которое требуется курьеру для доставки заказа.
                            Оно прибавляется к общему времени готовности заказа с учетом значений в таблице
                            «Интервалы доставки» с дополнительными выходными и рабочими днями.</span></div></wa-field>
                <customer-interval v-model="customer_interval"
                                   :ns="addns('customer_interval', info.namespace)"
                                   name="Желаемое время доставки и график работы"></customer-interval>
                <dates class="holidays"
                       name="Дополнительные выходные"
                       v-model="holidays"
                       :ns="addns('holidays', info.namespace)"></dates>
                <dates class="workdays"
                       name="Дополнительные рабочие дни"
                       v-model="workdays"
                       :ns="addns('workdays', info.namespace)"></dates>
                <wa-field name="Наценка"></wa-field>
                <wa-field name="Искючения"></wa-field>
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
    import DeliveryTime from '../components/DeliveryTimeControl.vue'
    import CustomerInterval from '../components/customer_interval/CustomerIntervalControl.vue'
    import Dates from '../components/dates/Dates.vue'

    export default {
        props: {
            info: {type: Object, required: true},
            settings: {type: Object, required: true}
        },
        mixins: [AddNs],
        data() {
            return this.settings
        },
        components: {LocationFrom, DeliveryTime, CustomerInterval, Dates}
    }
</script>
