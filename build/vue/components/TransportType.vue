<script>

export default {
    name: "TransportType",
    props: {
        ns: {type: String, default: ''},
        value: {type: Number, default: 0}
    },
    data() {
        return {
            transport_type: this.value,
            options: [
                {value: 0, title: 'Не передавать'},
                {value: 1, title: 'Легковой автомобиль / джип / пикап (до 500 кг)'},
                {value: 2, title: 'Каблук (до 700 кг)'},
                {value: 3, title: 'Микроавтобус / портер (до 1000 кг)'},
                {value: 4, title: 'Газель (до 1500 кг)'},
                {value: 5, title: 'Грузовой автомобиль'},
                {value: 6, title: 'Пеший курьер'},
                {value: 7, title: 'Легковой автомобиль'},
            ],
        }
    },
    watch: {
        transport_type(v) {
            this.$emit('input', v);
        }
    }
}

</script>

<template>

    <wa-field name="Тип транспорта">
        <div class="value">
            <ul>
                <li v-for="option in options" :key="option.value">
                    <label><input type="radio" :name="ns" v-model.number="transport_type" :value="option.value"> {{ option.title }}</label>
                </li>
            </ul>
            <span class="hint">Тип транспорта, которым должен быть доставлен заказ. Если не передавать никакого, то сервер Dostavista попробует определить тип транспорта исходя из параметров заказа.</span>
        </div>
    </wa-field>

</template>
