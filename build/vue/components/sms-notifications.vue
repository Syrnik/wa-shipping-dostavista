<template>
    <wa-field :name="name">
        <div class="value no-shift">
            <input type="hidden" :name="addns('client', ns)" value="0">
            <label><input type="checkbox" :name="addns('client', ns)" value="1" v-model="value.client"> &mdash; отправлять в магазин SMS о статусе заказа</label>
        </div>
        <div class="value">
            <h4>SMS получателю с интервалом прибытия и телефоном курьера</h4>
            <div><label><input type="radio" value="no" v-model="value.receiver" :name="addns('receiver', ns)"> &mdash; не отправлять</label></div>
            <div><label><input type="radio" value="yes" v-model="value.receiver" :name="addns('receiver', ns)"> &mdash; отправлять</label></div>
            <div><label><input type="radio" value="ask_no" v-model="value.receiver" :name="addns('receiver', ns)"> &mdash; спросить у покупателя, по умолчанию выключено</label></div>
            <div><label><input type="radio" value="ask_yes" v-model="value.receiver" :name="addns('receiver', ns)"> &mdash; спросить у покупателя, по умолчанию включено</label></div>
            <span class="hint">Если выбран вариант «спросить у покупателя…», то на этапе выбра доставки покупателю будет показан чекбокс и он сможет выбрать сам нужно ли ему получать SMS. Опция влияет на стоимость доставки, но в пошаговом режиме оформления заказа может работать некорректно (не вызывать пересчёт).</span>
        </div>
    </wa-field>
</template>

<script>
    import AddNs from './wa-namespace'

    export default {
        props: {
            name: {type: String, default: "SMS-уведомления"},
            ns: {type: String, default: ""},
            value: {type: Object, default(){return {client:false, receiver: "no"}}},
        },
        mixins: [AddNs]
    }
</script>
