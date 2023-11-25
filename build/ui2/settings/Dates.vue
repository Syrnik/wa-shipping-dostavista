<script setup>
import WaField from "../components/wa-field.vue";
import {computed, inject, onMounted, reactive, ref} from "vue";
import addns from "../components/addns";
import dateFormat, {masks} from "dateformat";
import datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const props = defineProps({
    name: {type: String, default: ''},
    fieldName: {type: String, default: ''},
    modelValue: {type: Array, default: () => []},
    buttonClass: {}
});

const emit = defineEmits(['update:modelValue']);

const selected_dates = ref(props.modelValue);
// const selected_dates = ref(['2023-11-02','2023-11-30', '2023-12-01']);
const ns = addns(props.fieldName, inject('namespace'));
const dark_mode = ref($('html').data('theme') === 'dark');
const calendar_open = ref(false);

const dates = computed({
    get() {
        return selected_dates.value.sort().map(d => new Date(d)).filter(d => (d instanceof Date) && !isNaN(d));
    },
    set(v) {
        selected_dates.value = v.sort().map(d => YMDdate(d));
        emit('update:modelValue', selected_dates.value);
    }
});

function YMDdate(v) {
    if (!(v instanceof Date) || isNaN(v)) return '';
    return dateFormat(v, 'yyyy-mm-dd')
}

function DMYdate(v) {
    if (!(v instanceof Date) || isNaN(v)) return '';
    return dateFormat(v, 'dd.mm.yyyy')
}

function deleteDate(ymd) {
    selected_dates.value = selected_dates.value.filter(v => v !== ymd);
    emit('update:modelValue', selected_dates.value);
}

</script>

<template>
    <wa-field :name="name">
        <ul class="w-shipping-syrnik-dates-list" v-if="dates.length">
            <li v-for="(d, idx) in dates"><span class="smallest button nowrap" :class="buttonClass">{{ DMYdate(d) }}
                    <a href="#" style="color: var(--red)" class="custom-ml-4" @click.prevent="deleteDate(YMDdate(d))"><i
                        class="red fas fa-times"></i></a></span><input
                type="hidden" :name="addns(idx, ns)"
                :value="YMDdate(d)"></li>
        </ul>
        <datepicker v-model="dates" multi-dates :dark="dark_mode" :class="{'custom-mt-12': !!dates.length}"
                    :day-names="['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']"
                    select-text="Выбрать"
                    cancel-text="Отмена"
                    locale="ru-RU"
                    :min-date="new Date()"
                    :enable-time-picker="false"
                    @open="calendar_open=true" @closed="calendar_open=false">
            <template #trigger>
                <button class="small button"><i class="far fa-calendar-alt"></i>
                    {{ calendar_open ? 'Закрыть' : 'Выбрать' }}
                </button>
            </template>
        </datepicker>
        <div>

        </div>
    </wa-field>
</template>

<style lang="stylus">
.w-shipping-syrnik-dates-list
    display block
    margin 0
    list-style-type none
    box-sizing border-box
    padding 0

    li
        box-sizing border-box
        margin 0 0.3em 0 0
        display inline-block

#w-shipping-dostavista-settings
    .dp__main
        display inline-block
        width auto
</style>
