<template>
    <wa-field :name="name">
        <div class="value">
            <input type="text"
                   class="long"
                   :name="addns('name', ns)"
                   placeholder="например: Москва, ул. Покровка, 3"
                   v-model="value.name">
        </div>
    </wa-field>
</template>

<script>
    import AddNs from '../components/wa-namespace'

    export default {
        props: {
            name: {type: String, default: ''},
            ns: {type: String, default: ''},
            value: {
                type: Object, default() {
                    return {name: ''}
                }
            }
        },
        mixins: [AddNs]
    }
</script>
