<template>
    <wa-field :name="name">
        <div class="value no-shift">
            <ul class="vue-dates-list">
                <li v-for="(d, idx) in highlighted.dates" :class="listItemClass"><input type="hidden" :name="addns(idx, ns)" :value="YMDdate(d)">{{ d | DMYdate }} <a href="#"><i class="icon10 no"></i></a></li>
            </ul>
        </div>
        <div class="value"><button
                @click.prevent="view.picker = !view.picker"><i class="icon16 calendar"></i> {{ view.picker ? 'Закрыть' : 'Выбрать' }}</button
        ><datepicker ref="dpicker"
                     inline
                     monday-first
                     v-if="view.picker"
                     :highlighted="highlighted"
                     :disabled-dates="{to: new Date()}"
                     :language="ru"
                     @selected="toggleDate"></datepicker></div>
    </wa-field>
</template>

<script>
    import AddNs from '../wa-namespace'
    import Datepicker from 'vuejs-datepicker'
    import ru from 'vuejs-datepicker/dist/locale/translations/ru'
    import dateformat from 'dateformat'

    export default {
        props: {
            name: {type: String, default: ''},
            ns: {type: String, default: ''},
            value: {type: Array, default: () => []},
            listItemClass: {type: String, default: ''}
        },
        mixins: [AddNs],
        components: {Datepicker},
        filters: {
            DMYdate(v) {
                if (!(v instanceof Date) || isNaN(v)) return '';
                return dateformat(v, 'dd.mm.yyyy')
            }
        },
        data() {
            return {
                view: { picker: false },
                ru: ru,
                selected_dates: this.value
            };
        },
        computed: {
            highlighted() {
                return {dates: this.selected_dates.sort().map(d => new Date(d)).filter(d => (d instanceof Date) && !isNaN(d)) };
            }
        },
        methods: {
            toggleDate(v) {
                if (v instanceof Date) {
                    const d = dateformat(v, 'yyyy-mm-dd');
                    const idx = this.value.findIndex(e => e === d);
                    if (idx === -1)
                        this.selected_dates.push(d);
                    else
                        this.selected_dates.splice(idx, 1);
                }
            },
            YMDdate(v) {
                if (!(v instanceof Date) || isNaN(v)) return '';
                return dateformat(v, 'yyyy-mm-dd')
            }
        }
    }
</script>

<style lang="stylus" scoped>
    .vue-dates-list
        display inline
        margin 0
        list-style-type none
        box-sizing border-box
        padding 0

        li
            box-sizing border-box
            margin 0 0.3em 0 0
            padding 3px
            border 1px solid transparent
            font-size 12px
            border-radius 3px
            line-height 12px
            height 20px
            display inline-block

            a
                vertical-align middle
                display inline-block
                box-sizing border-box

                i.icon10
                    margin 0
                    vertical-align initial
                    line-height 10px
                    box-sizing border-box

</style>