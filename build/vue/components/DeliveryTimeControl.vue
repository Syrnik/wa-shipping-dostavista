<template>
    <wa-field :name="name">
        <div class="value" v-for="(option, idx) in options" :key="option.value" :class="idx === 0 ? 'no-shift' : ''">
            <label><input type="radio" :name="ns" v-model="delivery_time" :value="option.value"> {{ option.label }}</label>
        </div>
    </wa-field>
</template>

<script>
    export default {
        props: {
            name: {type: String, default: 'Время доставки'},
            ns: {type: String, default: ''},
            options: {
                type: Array, default: () => [
                    {value: '', label: 'Не определено'},
                    {value: '+3 hour', label: 'В течение дня'},
                    {value: '+1 day', label: 'Следующий день'},
                    {value: '+1 day, +2 days', label: '1—2 дня'},
                    {value: '+2 days, +3 days', label: '2—3 дня'},
                    {value: '+1 week', label: '1 неделя'},
                    {value: 'exact_delivery_time', label: 'Указанное количество часов'}
                ]
            },
            value: {type: String}
        },
        data() {
            return {
                delivery_time: this.value
            };
        },
        watch: {
            delivery_time(v) {
                this.$emit('input', v);
            }
        }
    }
</script>
