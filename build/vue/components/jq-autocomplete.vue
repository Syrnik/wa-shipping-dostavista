<template>
    <input type="text" :value="label" :disabled="disabled">
</template>

<script>
    "use strict";

    export default {
        props: {
            customParams: {type: Object},
            source: Function,
            minLength: {type: Number, default: 2},
            value: {required: true},
            disabled: {type: Boolean, default: false},
            show: {type: String, default: 'label'}
        },
        beforeDestroy() {
            $(this.$el).autocomplete('destroy');
        },
        mounted() {
            const vm = this;
            $(this.$el).autocomplete({
                source: this.source,
                minLength: this.minLength,
                select: function (e, ui) {
                    e.preventDefault();
                    this.value = ui.item.label || '';
                    vm.$emit('input', ui.item);
                }
            });
            $(this.$el).attr('autocomplete', 'nope');
        },
        computed: {
            label() {
                return this.value[this.show];
            },
            query_string() {
                return '?' + $.param(this.customParams);
            }
        }
    }
</script>
