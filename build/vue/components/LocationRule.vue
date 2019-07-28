<template>
    <wa-field :name="name">
        <div class="value no-shift">
            <select :name="addns('type', addns(field, ns))"
                    v-model="value.type"><option
                    value="except">кроме</option><option
                    value="only">только</option></select>
            <input type="text" class="long"
                   :name="addns('value', addns(field, ns))"
                   v-model="value.value">
        </div>
        <div class="value" style="max-width: 650px"><span class="hint">
                        Названия городов через точку с запятой, код региона после двоеточия. Например «иваново; одинцово:50; :77»
                        запретит рассчитывать во все города, называющиеся «иваново», во все города 50-го региона, называющиеся
                        «одинцово» и вообще в любые города 77-го региона. Регистр названий городов неважен
                    </span></div>
    </wa-field>
</template>

<script>
    import AddNs from './wa-namespace'

    export default {
        props: {
            name: {type: String, default: "ограничение по географии"},
            ns: {type: String, default: ""},
            value: {type: Object, default(){return {type:"none", value: ""}}},
            field: {type: String, default: "location_rule"}
        },
        mixins:[AddNs]
    }
</script>
