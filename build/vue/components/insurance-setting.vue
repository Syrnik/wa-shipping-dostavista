<template>
    <wa-field :name="name">
        <div class="value no-shift">
            <label><input type="radio" :name="addns('type', addns(field, ns))" value="none" v-model="value.type"> Без страховки</label></div>
        <div class="value">
            <label><input type="radio" :name="addns('type', addns(field, ns))" value="raw" v-model="value.type"> Стоимость товара без скидок</label></div>
        <div class="value">
            <label><input type="radio" :name="addns('type', addns(field, ns))" value="total" v-model="value.type"> Стоимость товара со скидками</label></div>
        <div class="value">
            <label><input type="radio" :name="addns('type', addns(field, ns))" value="custom" v-model="value.type"> Фиксированная или формула</label></div>
        <div class="value" v-show="value.type === 'custom'">
            <input type="text" class="long" v-model="value.value" :name="addns('value', addns(field, ns))">
            <br><span class="hint"></span>
        </div>
    </wa-field>
</template>

<script>
    import AddNs from './wa-namespace'

    export default {
        props: {
            name: {type: String, default: "Сумма страховки"},
            ns: {type: String, default: ""},
            value: {type: Object, default(){return {type:"none", value: ""}}},
            field: {type: String, default: "insurance"}
        },
        mixins:[AddNs]
    }
</script>
