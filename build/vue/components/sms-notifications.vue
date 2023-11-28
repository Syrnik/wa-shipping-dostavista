<template>
    <wa-field :name="name">
        <div class="value no-shift">
            <input type="hidden" :name="addns('client', ns)" value="0">
            <label><input type="checkbox" :name="addns('client', ns)" value="1" v-model="value.client"> &mdash; отправлять в магазин SMS о статусе заказа</label>
        </div>
        <div class="value">
            <input type="hidden" :name="addns('receiver', ns)" value="no">
            <label><input type="checkbox" :name="addns('receiver', ns)" value="yes" v-model="value.receiver"> &mdash; отправлять получателю SMS с интервалом прибытия и телефоном курьера</label>
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
