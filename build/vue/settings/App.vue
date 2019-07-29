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
                <wa-field name="Наложенный платёж">
                    <div class="value no-shift">
                        <input type="hidden" :name="addns('cash_on_delivery', info.namespace)" value="0">
                        <label><input type="checkbox" :name="addns('cash_on_delivery', info.namespace)" v-model="cash_on_delivery" value="1"></label>
                    </div>
                    <div class="value"><span
                            class="hint">Если включить, то для расчёта будет также передана стоимость заказа (без учёта доставки), которую нужно получить у клиента. Возможно, это окажет влияние на стоимость доставки.</span></div></wa-field>
                <insurance field="insurance" v-model="insurance" :ns="info.namespace"></insurance>
                <wa-field name="Корректировка стоимости доставки"><div
                        class="value no-shift"><input
                        type="text"
                        class="long"
                        :name="addns('surcharge', info.namespace)"
                        placeholder="S"
                        v-model.trim="surcharge"><br><span
                        class="hint">Фиксированная стоимость или формула для наценки/скидки на расчётную стоимсть доставки. Можно оставить пустым, тогда будет использоваться стоимость, посчитанная через API. Доступные переменные: S — стоимость доставки, почитанная плагином, Z — стоимость заказа с учётом скидок, W — стоимость заказа без учёта скидок.</span></div></wa-field>
                <wa-field name="Порог бесплатной доставки">
                    <div class="value no-shift"><input
                            type="number"
                            class="short"
                            placeholder="Нет"
                            v-model.number="free_delivery"
                            min="0" step="0.01"
                            :name="addns('free_delivery', info.namespace)"><br><span
                            class="hint">Если сумма заказа больше либо равна указанной, то доставка в ПВЗ будет бесплатной. Оставьте поле пустым, если доставка всегда платная. Поставьте 0, если доставка всегда бесплатная.</span></div></wa-field>
                <location-rule name="Ограничения по географии" field="location_rule" :ns="info.namespace" v-model="location_rule"></location-rule>
                <wa-field name="Подробный лог">
                    <input type="hidden" :name="addns('detailed_log', info.namespace)" value="0">
                    <div class="value no-shift">
                        <label><input type="checkbox" :name="addns('detailed_log', info.namespace)" v-model="detailed_log"
                                      value="1"> &mdash; записывать подробный лог в режиме отладки</label><br><span
                            class="hint"><b>При включенном в системе режиме отладки</b> будет записываться не просто процесс расчёта, но и подробности по отправленным запросам и полученным ответам.</span>
                    </div>
                </wa-field>
            </tab>
            <tab name="Информация">
                <about-page :info="info"></about-page>
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
    import Insurance from '../components/insurance-setting.vue'
    import LocationRule from "../components/LocationRule.vue";
    import AboutPage from "../components/about/AboutPage.vue";

    export default {
        props: {
            info: {type: Object, required: true},
            settings: {type: Object, required: true}
        },
        mixins: [AddNs],
        data() {
            return this.settings
        },
        components: {LocationFrom, DeliveryTime, CustomerInterval, Dates, Insurance, LocationRule, AboutPage}
    }
</script>
