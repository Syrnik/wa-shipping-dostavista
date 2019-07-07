<template>
    <wa-field :name="name">
        <div class="value no-shift" v-if="date">
            <input type="hidden"
                   :name="addns('date', ns)"
                   value="0">
            <label><input type="checkbox"
                          :name="addns('date', ns)"
                          value="1"
                          v-model="value.date"> Запрашивать желаемую дату доставки</label></div>
        <div class="value" v-if="interval">
            <input type="hidden"
                   :name="addns('interval', ns)"
                   value="0">
            <label><input type="checkbox"
                          :name="addns('interval', ns)"
                          value="1"
                          v-model="value.interval"> Запрашивать желаемый интервал доставки</label></div>
        <div class="value" v-if="value.interval">
            <table class="zebra">
                <thead><tr>
                    <th colspan="2">Интервалы доставки</th>
                    <th v-for="v in ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']">{{ v }}</th>
                    <th v-if="holiday">Доп. выходной</th>
                    <th v-if="workday">Доп. рабочий день</th>
                    <th class="min-width"></th>
                </tr></thead>
                <tfoot><tr class="white">
                    <td colspan="2"><a class="inline-link"
                                       @click.prevent="AddInterval"><i class="icon16 add"></i><b><i>Добавить интервал</i></b></a></td>
                    <td colspan="7"></td>
                    <td v-if="holiday"></td>
                    <td v-if="workday"></td>
                    <td></td>
                </tr></tfoot>
                <tbody><tr is="item"
                           v-for="(val,idx) in value.intervals"
                           :ns="addns(idx, addns('intervals', ns))"
                           :value="val" @delete="DeleteInterval(idx)"></tr></tbody>
            </table>
        </div>
    </wa-field>
</template>

<script>
    import AddNs from '../wa-namespace'
    import Item from './CustomerIntervalItem.vue'

    function default_interval_row() {
        return {
            from: '10',
                from_m: '00',
            to: '12',
            to_m: '00',
            day: [1, 2, 3, 4, 5],
            workday: false,
            holiday: false
        };
    }

    export default {
        props: {
            name: {type: String, default: 'Время доставки'},
            ns: {type: String, default: ''},
            holiday: {type: Boolean, default: true},
            workday: {type: Boolean, default: true},
            interval: {type: Boolean, default: true},
            date: {type: Boolean, default: true},
            value: {
                type: Object,
                default() {
                    return {
                        date: true,
                        interval: true,
                        intervals: [default_interval_row()]
                    };
                }
            }
        },
        mixins: [AddNs],
        components: {Item},
        methods: {
            AddInterval() {
                this.value.intervals.push(default_interval_row());
            },
            DeleteInterval(idx) {
                if(this.value.intervals.length > 1) this.value.intervals.splice(idx, 1);
                else this.value.intervals = [default_interval_row()];
            }
        }
    }
</script>
